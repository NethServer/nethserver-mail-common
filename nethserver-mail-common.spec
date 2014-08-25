Summary: Common configuration for mail packages
Name: nethserver-mail-common
Version: 1.3.3
Release: 1%{?dist}
License: GPL
URL: %{url_prefix}/%{name} 
Source0: %{name}-%{version}.tar.gz
BuildArch: noarch

# nethserver-base requires postfix MTA:
Requires: nethserver-base >= 1.1.0-2

# amavisd-new is currently missing 
# - perl-Convert-BinHex, (issues  a startup warning)
# - /usr/sbin/tmpwatch command
Requires: amavisd-new >= 2.8.0-4, perl-Convert-BinHex, tmpwatch

BuildRequires: perl
BuildRequires: nethserver-devtools >= 1.0.0

%description
Common configuration for mail packages, based on Postfix.

%prep
%setup

%build
perl createlinks

%install
rm -rf $RPM_BUILD_ROOT
(cd root; find . -depth -print | cpio -dump $RPM_BUILD_ROOT)
%{genfilelist} $RPM_BUILD_ROOT \
   --dir /var/lib/nethserver/mail-disclaimers 'attr(2775,root,adm)' \
   > %{name}-%{version}-filelist
echo "%doc COPYING" >> %{name}-%{version}-filelist

%clean
rm -rf $RPM_BUILD_ROOT

%post

%preun

%files -f %{name}-%{version}-filelist
%defattr(-,root,root)

%changelog
* Fri Jun 06 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.3-1.ns6
- Disclaimer creation fails - Bug #2724
- Mail: Incorrect value RelayPort_label - Bug #2713

* Wed Feb 05 2014 Davide Principi <davide.principi@nethesis.it> - 1.3.2-1.ns6
- Mail server: can't send mail bigger than 50MB - Bug #2634 [NethServer]

* Mon Sep 02 2013 Davide Principi <davide.principi@nethesis.it> - 1.3.1-1.ns6
- SMTP temporary error on non-existing recipients - Bug #2108 [NethServer]
- amavisd-new 2.8.0 from EPEL - Enhancement #2093 [NethServer]

* Mon Jul 29 2013 Davide Principi <davide.principi@nethesis.it> - 1.3.0-1.ns6
- Mail-common: email queue management - Feature #2042 [NethServer]

* Mon Jun 10 2013 Davide Principi <davide.principi@nethesis.it> - 1.2.1-1.ns6
- Spawn more amavisd wokers #1908
- Timeout after END-OF-MESSAGE from localhost #1968

* Tue Apr 30 2013 Davide Principi <davide.principi@nethesis.it> - 1.2.0-1.ns6
- Full automatic package install/upgrade/uninstall support #1870 #1872 #1874
- Allow submission policy overriding in dependant packages and DB #1818 #1856
- Added default empty disclaimer files to workaround altermime SIGSEGVs #1819

* Tue Mar 19 2013 Davide Principi <davide.principi@nethesis.it> - 1.1.0-1.ns6
- MX record configuration. Refs #1725
- *.spec: use url_prefix macro in URL tag; set minimum version requirements. Refs #1654 #1653

* Thu Jan 31 2013 Davide Principi <davide.principi@nethesis.it> - 1.0.1-1.ns6
- Postfix installation moved to nethserver-base . Refs #1635 -- admin's mailbox


