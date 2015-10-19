#define PID_PATH        "/run/nakd/nakd.pid"
#define SOCK_PATH       "/run/nakd/nakd.sock"

/* Enough to hold any %ld. */
#define PID_STR_LEN     128

void p_error(const char *ctx, const char *err);
int handle_connection(int sock);
char *handle_message(char *message_buf);
