#include <stdio.h>
#include <stdarg.h>
#include <string.h>
#include <syslog.h>
#include "log.h"

static int use_syslog = 1;
static int loglevel = DEFAULT_LOG_LEVEL;

static const int log_class[] = {
    [L_CRIT] = LOG_CRIT,
    [L_WARNING] = LOG_WARNING,
    [L_NOTICE] = LOG_NOTICE,
    [L_INFO] = LOG_INFO,
    [L_DEBUG] = LOG_DEBUG
};

void nakd_set_loglevel(int level) {
    loglevel = level;
}

void nakd_use_syslog(int use) {
    use_syslog = use;
}

void nakd_log_init() {
    openlog("nakd", 0, LOG_DAEMON);
}

void nakd_log_close() {
    closelog();
}

void nakd_log(int priority, const char *format, ...) {
    va_list vl;

    if (priority > loglevel)
        return;

    va_start(vl, format);
    if (use_syslog)
        vsyslog(log_class[priority], format, vl);
    else
        vfprintf(stderr, format, vl);
    va_end(vl);
}
