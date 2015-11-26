#ifndef CONFIG_H
#define CONFIG_H
#include <uci.h>

struct uci_package *nakd_load_uci_package(const char *name);
int nakd_uci_save(struct uci_package *pkg);
int nakd_uci_commit(struct uci_package **pkg, bool overwrite);
int nakd_unload_uci_package(struct uci_package *pkg);

#endif
