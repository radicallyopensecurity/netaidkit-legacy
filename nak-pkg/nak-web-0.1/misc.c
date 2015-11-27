#include <stdio.h>
#include <stdlib.h>
#include <errno.h>
#include <string.h>
#include "misc.h"
#include "log.h"

/* TODO: PASS FORMAT STRING AND VARIABLE AMOUNT OF ARGS */
void p_error(const char *ctx, const char *err) {
    nakd_log(L_CRIT, "nakd: %s: %s\n", ctx,
        (err == NULL) ? strerror(errno) : err);
    exit(-1);
}

void __nakd_assert(int stmt, const char *stmt_str, const char *func) {
    if (stmt)
        return;

    nakd_log(L_CRIT, "nakd: assertion (%s) failed in %s\n",
                                           stmt_str, func);
    exit(-1);
}
