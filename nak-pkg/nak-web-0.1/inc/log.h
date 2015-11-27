#ifndef LOG_H
#define LOG_H
#define DEFAULT_LOG_LEVEL L_NOTICE

enum {
    L_CRIT,
    L_WARNING,
    L_NOTICE,
    L_INFO,
    L_DEBUG
};

void nakd_set_loglevel(int level);
void nakd_use_syslog(int use);
void nakd_log_init();
void nakd_log_close();
void nakd_log(int priority, const char *format, ...);

#endif
