dnl
dnl $Id$
dnl

PHP_ARG_ENABLE(zip, for zip archive read/writesupport,
[  --enable-zip            Include Zip read/write support])

PHP_ARG_WITH(libzip, libzip,
[  --with-libzip[=DIR]       ZIP: use libzip], yes, no)

if test "$PHP_ZIP" != "no"; then

  AC_MSG_CHECKING(PHP version)
  if test -d $abs_srcdir/php7 ; then
    dnl # only when for PECL, not for PHP
    export OLD_CPPFLAGS="$CPPFLAGS"
    export CPPFLAGS="$CPPFLAGS $INCLUDES"
    AC_TRY_COMPILE([#include <php_version.h>], [
#if PHP_MAJOR_VERSION >= 7 && PHP_MINOR_VERSION >= 3
#error  PHP >= 7.3
#endif
    ], [
      AC_TRY_COMPILE([#include <php_version.h>], [
#if PHP_MAJOR_VERSION > 5
#error  PHP > 5
#endif
      ], [
        subdir=php5
        AC_MSG_RESULT([PHP 5.x])
      ], [
        subdir=php7
        AC_MSG_RESULT([PHP 7.0 - 7.2])
      ])
    ], [
      subdir=php73
      AC_MSG_RESULT([PHP >= 7.3])
    ])
    export CPPFLAGS="$OLD_CPPFLAGS"
    PHP_ZIP_SOURCES="$subdir/php_zip.c $subdir/zip_stream.c"
  else
    AC_MSG_ERROR([unknown])
    PHP_ZIP_SOURCES="php_zip.c zip_stream.c"
  fi

  if test "$PHP_LIBZIP" != "no"; then

    AC_PATH_PROG(PKG_CONFIG, pkg-config, no)

    AC_MSG_CHECKING(for libzip)
    if test -r $PHP_LIBZIP/include/zip.h; then
      LIBZIP_CFLAGS="-I$PHP_LIBZIP/include"
      LIBZIP_LIBDIR="$PHP_LIBZIP/$PHP_LIBDIR"
      AC_MSG_RESULT(from option: found in $PHP_LIBZIP)

    elif test -x "$PKG_CONFIG" && $PKG_CONFIG --exists libzip; then
      if $PKG_CONFIG libzip --atleast-version 0.11; then
        LIBZIP_CFLAGS=`$PKG_CONFIG libzip --cflags`
        LIBZIP_LIBDIR=`$PKG_CONFIG libzip --variable=libdir`
        LIBZIP_VERSON=`$PKG_CONFIG libzip --modversion`
        AC_MSG_RESULT(from pkgconfig: version $LIBZIP_VERSON found in $LIBZIP_LIBDIR)
      else
        AC_MSG_ERROR(system libzip must be upgraded to version >= 0.11)
      fi

    else
      for i in /usr/local /usr; do
        if test -r $i/include/zip.h; then
          LIBZIP_CFLAGS="-I$i/include"
          LIBZIP_LIBDIR="$i/$PHP_LIBDIR"
          AC_MSG_RESULT(in default path: found in $i)
          break
        fi
      done
    fi

    if test -z "$LIBZIP_LIBDIR"; then
      AC_MSG_RESULT(not found)
      AC_MSG_ERROR(Please reinstall the libzip distribution)
    fi

    dnl Could not think of a simple way to check libzip for overwrite support
    PHP_CHECK_LIBRARY(zip, zip_open,
    [
      PHP_ADD_LIBRARY_WITH_PATH(zip, $LIBZIP_LIBDIR, ZIP_SHARED_LIBADD)
      AC_DEFINE(HAVE_LIBZIP,1,[ ])
    ], [
      AC_MSG_ERROR(could not find usable libzip)
    ], [
      -L$LIBZIP_LIBDIR
    ])

    PHP_CHECK_LIBRARY(zip, zip_file_set_encryption,
    [
      AC_DEFINE(HAVE_ENCRYPTION, 1, [Libzip >= 1.2.0 with encryption support])
    ], [
      AC_MSG_WARN(Libzip >= 1.2.0 needed for encryption support)
    ], [
      -L$LIBZIP_LIBDIR
    ])

    PHP_CHECK_LIBRARY(zip, zip_libzip_version,
    [
      AC_DEFINE(HAVE_LIBZIP_VERSION, 1, [Libzip >= 1.3.1 with zip_libzip_version function])
    ], [
    ], [
      -L$LIBZIP_LIBDIR
    ])

    AC_DEFINE(HAVE_ZIP,1,[ ])
    PHP_NEW_EXTENSION(zip, $PHP_ZIP_SOURCES, $ext_shared,, $LIBZIP_CFLAGS)
  else
    AC_MSG_ERROR([libzip is no more bundled: install libzip version >= 0.11 (1.3.0 recommended for encryption and bzip2 support)])
  fi

  if test -n "$subdir" ; then
    PHP_ADD_BUILD_DIR($abs_builddir/$subdir, 1)
    PHP_ADD_INCLUDE([$ext_srcdir/$subdir])
  fi
  PHP_SUBST(ZIP_SHARED_LIBADD)

  dnl so we always include the known-good working hack.
  PHP_ADD_MAKEFILE_FRAGMENT
fi
