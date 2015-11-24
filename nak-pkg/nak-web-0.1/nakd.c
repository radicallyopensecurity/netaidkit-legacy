#include <nakd.h>
#include <stdio.h>
#include <fcntl.h>
#include <string.h>
#include <command.h>
#include <message.h>
#include <linux/un.h>
#include <sys/stat.h>
#include <sys/wait.h>
#include <sys/types.h>
#include <sys/socket.h>
#include "misc.h"

int handle_connection(int sock) {
    struct sockaddr_un client_addr;
    socklen_t client_len = sizeof(struct sockaddr_un);
    char *response, message_buf[MAX_MSG_LEN + 1];
    int n;

    memset(message_buf, 0x00, MAX_MSG_LEN + 1);

    /* TODO: GRAB CREDENTIALS OF CONNECTING PROCESS */
    printf("CONNECTION FROM USER: [%s]\n", "USER");

    if ((n = recvfrom(sock, message_buf, MAX_MSG_LEN, 0,
                      (struct sockaddr *) &client_addr, &client_len)) < 0)
        p_error("recvfrom()", NULL);

    message_buf[n] = 0x00;

    if ((response = handle_message(message_buf)) == NULL)
        p_error("handle_message()", "Could not generate response.");

    if (sendto(sock, response, strlen(response), 0,
               (struct sockaddr *) &client_addr, client_len) < strlen(response))
        p_error("sendto()", NULL);

    free(response);
    return 0;
}

char *handle_message(char *message_buf) {
    message *msg;
    command *cmd = NULL;
    char *response = NULL;

    if ((msg = parse_message(MSG_TYPE_COMMAND, message_buf)) == NULL)
        return strdup("Invalid command.\n");

    if (msg->cmd);
        response = msg->cmd->handler(msg->args);

    return response;
}

/* Create file containing pid as a string and obtain a write lock for it. */
int writePid(char *pid_path) {
    int fd;
    struct flock pid_lock;
    char pid_str[PID_STR_LEN + 1];

    if ((fd = open(pid_path, O_RDWR | O_CREAT, S_IRUSR | S_IWUSR)) == -1)
        p_error("open()", NULL);

    pid_lock.l_type = F_WRLCK;
    pid_lock.l_whence = SEEK_SET;
    pid_lock.l_start = 0;
    pid_lock.l_len = 0;

    if (fcntl(fd, F_SETLK, &pid_lock) == -1)
        return -1;

    if (ftruncate(fd, 0) == -1)
        p_error("ftruncate()", NULL);

    snprintf(pid_str, PID_STR_LEN + 1, "%ld\n", (long) getpid());
    if (write(fd, pid_str, strlen(pid_str)) != strlen(pid_str))
        p_error("write()", "Could not write to pidfile.");

    return fd;
}

int main(int argc, char *argv[]) {
    struct stat sock_path_st;
    struct sockaddr_un server;
    int pid_fd, sock, n_sock_path = strlen(SOCK_PATH);

    /* Check if nakd is already running. */
    if ((pid_fd = writePid(PID_PATH)) == -1)
        p_error("writePid()", "nakd is already running.");

    /* TODO: CHECK IF CURRENT USER IS ROOT AND IF NAKD USER EXISTS */

    /* Create the nakd server socket. */
    if ((sock = socket(AF_UNIX, SOCK_STREAM, 0)) == -1)
        p_error("socket()", NULL);

    /* Check if SOCK_PATH is strncpy safe. */
    if (n_sock_path >= UNIX_PATH_MAX)
        p_error("main()", "Socket path too long.");

    /* Set domain socket path to SOCK_PATH. */
    memset(&server, 0x0, sizeof(struct sockaddr_un));
    strncpy(server.sun_path, SOCK_PATH, n_sock_path);
    server.sun_family = AF_UNIX;

    /* TODO: ADD P_INFO FUNCTION */
    printf("[INFO]\tnakd: Using socket at %s\n", server.sun_path);

    /* Remove domain socket file if it exists. */
    if (stat(SOCK_PATH, &sock_path_st) == 0)
        if (unlink(SOCK_PATH) == -1)
            p_error("unlink()", NULL);

    /* Bind nakd server socket to the domain socket. */
    if (bind(sock, (struct sockaddr *) &server, sizeof(struct sockaddr_un)) < 0)
         p_error("bind()", NULL);

     /* Set domain socket world writable, permissions via credentials passing */
     if (chmod(SOCK_PATH, 0777) == -1)
        p_error("chmod()", "Could not make domain socket world writable.");

    /* Listen on local domain socket. */
    if (listen(sock, 5) == -1)
        p_error("listen()", NULL);

    /* Main nakd loop. */
    for(;;) {
        int c_sock;
        socklen_t len;
        pid_t handler_pid;
        struct sockaddr_un client;

        len = sizeof(client);
        if ((c_sock = accept(sock, (struct sockaddr *) &client, &len)) == -1)
            p_error("accept", NULL);

        if ((handler_pid = fork()) == -1)
            p_error("fork", NULL);
        else if (handler_pid == 0) {
            return handle_connection(c_sock);
        } else {
            waitpid(handler_pid, NULL, WUNTRACED);
        }

        close(c_sock);
    }

    close(sock);
    if (unlink(SOCK_PATH) == -1)
        p_error("unlink()", NULL);

    return 0;
}
