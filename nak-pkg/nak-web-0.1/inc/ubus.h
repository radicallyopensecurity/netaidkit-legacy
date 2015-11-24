#ifndef UBUS_H
#define UBUS_H
#include <libubus.h>

int nakd_ubus_init();
int nakd_ubus_call(const char *namespace, const char* procedure,
       const char *arg, ubus_data_handler_t cb, void *cb_priv);
int nakd_ubus_free();

#endif
