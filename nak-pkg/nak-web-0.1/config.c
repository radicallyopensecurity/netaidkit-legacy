#include <uci.h>
#include "config.h"
#include "misc.h"

static struct uci_context *uci_ctx;

static int init_uci_ctx() {
    if (!uci_ctx)
        uci_ctx = uci_alloc_context();
    return !(uci_ctx != NULL);
}

struct uci_package *nakd_load_uci_package(const char *name) {
    struct uci_package *pkg = NULL;

    nakd_assert(name != NULL);
    
    if (init_uci_ctx())
        return NULL;

    if (uci_load(uci_ctx, name, &pkg))
        return NULL;

    return pkg;
}

int nakd_uci_save(struct uci_package *pkg) {
    return uci_save(uci_ctx, pkg);
}

int nakd_uci_commit(struct uci_package **pkg, bool overwrite) {
    return uci_commit(uci_ctx, pkg, overwrite);
}

int nakd_unload_uci_package(struct uci_package *pkg) {
    return uci_unload(uci_ctx, pkg);
}
