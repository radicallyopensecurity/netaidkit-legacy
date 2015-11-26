#include <uci.h>
#include "hooks.h"
#include "config.h"
#include "misc.h"

int nakd_call_uci_hooks(const char *package,
    struct nakd_uci_hook *hook_list, const char *state) {
    struct nakd_uci_hook *hook;
    struct uci_element *sel, *lel;
    struct uci_section *section;
    struct uci_option *option;
    struct uci_package *uci_pkg;
    struct uci_context *ctx;
    
    uci_pkg = nakd_load_uci_package(package);
    if (package == NULL)
        return 1;

    /* for each hook */
    for (hook = hook_list; hook->name != NULL; hook++) {
        nakd_assert(hook->handler != NULL);

        /*
         * Iterate through sections, ie.
         *  config redirect
         */
        uci_foreach_element(&uci_pkg->sections, sel) {
            section = uci_to_section(sel);

            option = uci_lookup_option(uci_pkg->ctx, section, hook->name);
            if (option == NULL)
                continue;

            if (option->type == UCI_TYPE_STRING) {
                hook->handler(hook->name, state, option);
            } else if (option->type == UCI_TYPE_LIST) {
                /*
                 * ...and through options, which are in fact lists
                 *  list nak_hooks_disable   'stage_online'
                 *  list nak_hooks_disable   'stage_vpn'
                 */
                uci_foreach_element(&option->v.list, lel) {
                    hook->handler(hook->name, state, option);
                }
            } else {
                nakd_assert(!(int)("unreachable"));
            }
            if (nakd_uci_save(uci_pkg))
                return 1;
        }
    }

    /* nakd probably wouldn't recover from these */
    nakd_assert(!nakd_uci_commit(&uci_pkg, true));
    nakd_assert(!nakd_unload_uci_package(uci_pkg));
    return 0;
}
