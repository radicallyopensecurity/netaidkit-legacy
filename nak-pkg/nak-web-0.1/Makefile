LDLIBS = -ljson-c -lubus -lubox -lblobmsg_json

nakd: nakd.c
	$(CC) $(CFLAGS) $(LDFLAGS) $(LDLIBS) -I inc nakd.c command.c wifi.c message.c wrap.c inet.c stage.c update.c misc.c ubus.c -o nakd

clean:
	rm -f nakd
