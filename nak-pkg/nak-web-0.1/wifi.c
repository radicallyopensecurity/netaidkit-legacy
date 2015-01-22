#include <stdio.h>
#include <stdlib.h>
#include <iwlib.h>
#include <wifi.h>

/*  scan_wifi()
 *  Expects 0 arguments.
 */
int scan_wifi(char *args[])
{   
    wireless_scan_head head;
    wireless_scan *result;
    iwrange range;
    int sock;

    sock = iw_sockets_open();

    if (iw_get_range_info(sock, "wlp2s0", &range) < 0) {
        fprintf(stderr, "iw_get_range_info failed.\n");
        return -1;
    }

    if (iw_scan(sock, "wlp2s0", range.we_version_compiled, &head) < 0) {
        fprintf(stderr, "iw_scan failed.\n");
        return -1;
    }

    result = head.result;
    while (result != NULL) {
        printf("%s\n", result->b.essid);
        result = result->next;
    }
    
    return 0;
}
