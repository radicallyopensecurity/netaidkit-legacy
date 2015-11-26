#ifndef HOOKS_H
#define HOOKS_H
#include <uci.h>

struct nakd_uci_hook {
    const char *name;
    void (*handler)(const char *name, const char *state,
        struct uci_option *option);
};

int nakd_call_uci_hooks(const char *package, struct nakd_uci_hook *hook_list,
                                                           const char *state);

#endif
