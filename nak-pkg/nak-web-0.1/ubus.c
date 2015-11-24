#include <unistd.h>
#include <libubox/blobmsg_json.h>
#include <libubus.h>
#include "misc.h"

#define UBUS_CALL_TIMEOUT 15 * 1000

static struct ubus_context *ubus_ctx = NULL;
static struct blob_buf ubus_buf;

int nakd_ubus_init() {
    /* defaults to UBUS_UNIX_SOCKET */
    ubus_ctx = ubus_connect(NULL);
    return ubus_ctx == NULL ? 1 : 0;
}

int nakd_ubus_call(const char *namespace, const char* procedure,
       const char *arg, ubus_data_handler_t cb, void *cb_priv) {
    int namespace_id;
    int lookup_err;

    nakd_assert(namespace != NULL && procedure != NULL &&
                                arg != NULL && cb!= NULL);

    /* subsequent inits free previous data */
    blob_buf_init(&ubus_buf, 0);

    if (arg != NULL) {
        if (!blobmsg_add_json_from_string(&ubus_buf, arg)) {
            // TODO syslog noncritical parse error
            return 1;
        }
    }

    lookup_err = ubus_lookup_id(ubus_ctx, namespace, &namespace_id);
    if (lookup_err)
        return lookup_err;

    return ubus_invoke(ubus_ctx, namespace_id, procedure, ubus_buf.head,
                                        cb, cb_priv, UBUS_CALL_TIMEOUT);
}

int nakd_ubus_free() {
    if (ubus_buf.buf != NULL)
        blob_buf_free(&ubus_buf);
    if (ubus_ctx != NULL)
        ubus_free(ubus_ctx);
}
