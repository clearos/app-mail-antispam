
Name: app-mail-antispam
Epoch: 1
Version: 1.6.5
Release: 1%{dist}
Summary: Mail Antispam
License: GPLv3
Group: ClearOS/Apps
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base
Requires: app-network

%description
The Antispam app provides a mail filter that uses a wide range of heuristic tests to identify spam.

%package core
Summary: Mail Antispam - Core
License: LGPLv3
Group: ClearOS/Libraries
Requires: app-base-core
Requires: spamassassin
Requires: app-base-core >= 1:1.6.5
Requires: app-mail-filter-core >= 1:1.6.5
Requires: app-smtp-core
Requires: app-tasks-core

%description core
The Antispam app provides a mail filter that uses a wide range of heuristic tests to identify spam.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/mail_antispam
cp -r * %{buildroot}/usr/clearos/apps/mail_antispam/

install -D -m 0644 packaging/app-mail-antispam.cf %{buildroot}/etc/mail/spamassassin/app-mail-antispam.cf

%post
logger -p local6.notice -t installer 'app-mail-antispam - installing'

%post core
logger -p local6.notice -t installer 'app-mail-antispam-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/mail_antispam/deploy/install ] && /usr/clearos/apps/mail_antispam/deploy/install
fi

[ -x /usr/clearos/apps/mail_antispam/deploy/upgrade ] && /usr/clearos/apps/mail_antispam/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-mail-antispam - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-mail-antispam-core - uninstalling'
    [ -x /usr/clearos/apps/mail_antispam/deploy/uninstall ] && /usr/clearos/apps/mail_antispam/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/mail_antispam/controllers
/usr/clearos/apps/mail_antispam/htdocs
/usr/clearos/apps/mail_antispam/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/mail_antispam/packaging
%dir /usr/clearos/apps/mail_antispam
/usr/clearos/apps/mail_antispam/deploy
/usr/clearos/apps/mail_antispam/language
/usr/clearos/apps/mail_antispam/libraries
/etc/mail/spamassassin/app-mail-antispam.cf
