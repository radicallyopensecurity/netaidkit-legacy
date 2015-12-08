BUILD = .

SRC = $(wildcard *.c)
OBJ = $(patsubst %.c, $(BUILD)/%.o, $(SRC))
DEPEND = $(patsubst %.c, $(BUILD)/%.d, $(SRC))

.PRECIOUS: $(DEPEND)

INC = -Iinc
CFLAGS += $(INC)

LDLIBS += -ljson-c -lubus -lubox -lblobmsg_json -luci

TARGETS = $(BUILD)/nakd

all: $(TARGETS)

-include $(DEPEND)

$(BUILD)/nakd: $(OBJ)
	$(CC) $(LDFLAGS) $(OBJ) $(LDLIBS) -o $@

$(BUILD)/%.d: %.c
	$(CC) $(CFLAGS) -MM $< -o $(BUILD)/$*.d

$(BUILD)/%.o: %.c $(BUILD)/%.d
	$(CC) -c $(CFLAGS) $< -o $(BUILD)/$*.o

clean:
	rm -f $(TARGETS) $(OBJ) $(DEPEND)

.PHONY: all clean
.DEFAULT: all
