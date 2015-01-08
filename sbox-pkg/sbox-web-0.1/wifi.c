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

    /* Open socket to kernel */
    sock = iw_sockets_open();

    /* Get some metadata to use for scanning */
    if (iw_get_range_info(sock, "wlp2s0", &range) < 0) {
        printf("Error during iw_get_range_info. Aborting.\n");
        return -1;
    }

    /* Perform the scan */
    if (iw_scan(sock, "wlp2s0", range.we_version_compiled, &head) < 0) {
        printf("Error during iw_scan. Aborting.\n");
        return -1;
    }

    /* Traverse the results */
    result = head.result;
    while (result != NULL) {
        printf("%s\n", result->b.essid);
        result = result->next;
    }
    
    return 0;
}
