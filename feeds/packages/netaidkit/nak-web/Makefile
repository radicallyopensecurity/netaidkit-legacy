include $(TOPDIR)/rules.mk

PKG_NAME:=nak-web
PKG_VERSION:=0.1
PKG_RELEASE:=1

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tar.xz
PKG_BUILD_DIR:=$(BUILD_DIR)/$(PKG_NAME)-$(PKG_VERSION)

include $(INCLUDE_DIR)/package.mk

define Package/$(PKG_NAME)    
	SECTION:=NetAidKit
	CATEGORY:=NetAidKit
	TITLE:=$(PKG_NAME)
	DEPENDS:=+lighttpd +lighttpd-mod-cgi
endef

define Package/$(PKG_NAME)/description
	The web interface for the NetAidKit.
endef

CONFIGURE_VARS+= \
	CC="$(TOOLCHAIN_DIR)/bin/$(TARGET_CC)"

define Package/$(PKG_NAME)/install
	$(INSTALL_DIR) $(1)/usr/bin
	$(INSTALL_DIR) $(1)/www
	$(INSTALL_BIN) $(PKG_BUILD_DIR)/netaidkit $(1)/usr/bin/netaidkit
	chmod +s $(1)/usr/bin/netaidkit
endef

$(eval $(call BuildPackage,$(PKG_NAME)))
