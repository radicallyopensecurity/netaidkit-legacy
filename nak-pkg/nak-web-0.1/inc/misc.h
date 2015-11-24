#ifndef MISC_H
#define MISC_H

void p_error(const char *ctx, const char *err);

#define nakd_assert(stmt) __nakd_assert((stmt), #stmt, __PRETTY_FUNCTION__)
void __nakd_assert(int stmt, const char *stmt_str, const char *func);

#endif
