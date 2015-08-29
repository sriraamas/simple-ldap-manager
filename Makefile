export VERSION ?= $(shell cat version)

all: package

clean:
	rm -fr dist

package: dist/simple-ldap-manager.deb

dist/simple-ldap-manager.deb: dist/deb/DEBIAN/control
	@mkdir -p dist/deb/opt/simple-ldap-manager
	rsync -dr                      \
		--exclude=.git             \
		--exclude /deb             \
		--exclude /DEBIAN/control  \
		--exclude /$@              \
		--delete                   \
		--quiet                    \
		. dist/deb/opt/simple-ldap-manager
	fakeroot dpkg-deb --build dist/deb $@
	@printf '$@ - %0.1f MB\n' $$(echo `wc -c < $@` / 1024 / 1024 | bc -l)

dist/deb/DEBIAN/control: DEBIAN/control version
	@mkdir -p $(@D)
	php $^ > $@
