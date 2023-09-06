SRC_PATH := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))
BIN_PATH := $(abspath $(SRC_PATH)/../bin)

ARCHIVE = $(BIN_PATH)/UnraidFanControl.txz
PLG = $(BIN_PATH)/UnraidFanControl.plg

.PHONY: all plugin $(ARCHIVE)

all: plugin
	@echo Build complete

plugin: $(ARCHIVE) $(PLG)

$(ARCHIVE): $(BIN_PATH)
	@echo Building archive
	@tar -C $(SRC_PATH) -cf - usr/ | xz -z - > $@

$(PLG): $(SRC_PATH)/UnraidFanControl.plg $(ARCHIVE)
	@echo Building package checksum
	@sed 's/$${MD5_PACKAGE}/$(shell md5sum $(ARCHIVE) | awk '{print $$1}')/' $< > $@

$(BIN_PATH):
	@mkdir -p $@
