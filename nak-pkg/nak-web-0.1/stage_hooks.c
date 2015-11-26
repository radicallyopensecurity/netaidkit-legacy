#include <uci.h>
#include <string.h>
#include "stage_hooks.h"
#include "hooks.h"
#include "misc.h"

static void toggle_rule(const char *hook_name, const char *state,
                                    struct uci_option *option) {
    nakd_assert(hook_name != NULL && state != NULL && option != NULL);
    
    struct uci_context *ctx = option->section->package->ctx;
    struct uci_section *section = option->section;
    struct uci_option *opt_enabled =
        uci_lookup_option(ctx, section, "enabled");
    
    struct uci_ptr new_opt_enabled_ptr = {
        .target = UCI_TYPE_OPTION,
        .p = option->section->package,
        .s = option->section
    };

    const char *cvalue = strcmp(hook_name, "nak_hooks_enable") ?
        "option enabled 1" : "option enabled 0";
    char *value = strdup(cvalue);

    uci_parse_ptr(ctx, &new_opt_enabled_ptr, value);
    uci_set(ctx, &new_opt_enabled_ptr);
}

struct nakd_uci_hook rule_hooks[] = {
    {"nak_hooks_enable", toggle_rule},
    {"nak_hooks_disable", toggle_rule},
    {NULL, NULL}
};

void nakd_call_stage_hooks(const char *stage) {
    nakd_call_uci_hooks("firewall", rule_hooks, stage);
}
