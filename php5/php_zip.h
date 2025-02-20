/*
  +----------------------------------------------------------------------+
  | PHP Version 5                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) The PHP Group                                          |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt.                                 |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author: Pierre-Alain Joye <pajoye@php.net>                           |
  +----------------------------------------------------------------------+
*/


#ifndef PHP_ZIP_H
#define PHP_ZIP_H

extern zend_module_entry zip_module_entry;
#define phpext_zip_ptr &zip_module_entry

#ifdef ZTS
#include "TSRM.h"
#endif

#include <zip.h>

#ifndef ZIP_OVERWRITE
#define ZIP_OVERWRITE ZIP_TRUNCATE
#endif

/* since 1.10.1 */
#ifndef ZIP_LENGTH_TO_END
#define ZIP_LENGTH_TO_END 0
#endif

/* Additionnal flags not from libzip */
#define ZIP_FL_OPEN_FILE_NOW (1u<<30)

#define PHP_ZIP_VERSION "1.22.5"

/* {{{ ZIP_OPENBASEDIR_CHECKPATH(filename) */
#define ZIP_OPENBASEDIR_CHECKPATH(filename) \
	php_check_open_basedir(filename TSRMLS_CC)
/* }}} */

typedef struct _ze_zip_rsrc {
	struct zip *za;
	zip_uint64_t index_current;
	zip_uint64_t num_files;
} zip_rsrc;

typedef zip_rsrc * zip_rsrc_ptr;

typedef struct _ze_zip_read_rsrc {
	struct zip_file *zf;
	struct zip_stat sb;
	/* Used to check if the zip resource still exists,
	 * without holding a reference. This works because the IDs are unique. */
	int zip_rsrc_handle;
} zip_read_rsrc;

#define ZIPARCHIVE_ME(name, arg_info, flags) ZEND_ME(ZipArchive, name, arg_info, flags)
#define ZIPARCHIVE_METHOD(name)	ZEND_METHOD(ZipArchive, name)

/* Extends zend object */
typedef struct _ze_zip_object {
	zend_object zo;
	struct zip *za;
	int buffers_cnt;
	char **buffers;
	HashTable *prop_handler;
	char *filename;
	int filename_len;
	zip_int64_t last_id;
	int err_zip;
	int err_sys;
#ifdef HAVE_PROGRESS_CALLBACK
	zval *progress_callback;
#endif
#ifdef HAVE_CANCEL_CALLBACK
	zval *cancel_callback;
#endif
} ze_zip_object;

#if PHP_VERSION_ID < 50600
php_stream *php_stream_zip_opener(php_stream_wrapper *wrapper,       char *path,       char *mode, int options, char **opened_path, php_stream_context *context STREAMS_DC TSRMLS_DC);
#else
php_stream *php_stream_zip_opener(php_stream_wrapper *wrapper, const char *path, const char *mode, int options, char **opened_path, php_stream_context *context STREAMS_DC TSRMLS_DC);
#endif
php_stream *php_stream_zip_open(struct zip *arch, struct zip_stat *sb, const char *mode, zip_flags_t flags STREAMS_DC TSRMLS_DC);

extern php_stream_wrapper php_stream_zip_wrapper;

#define LIBZIP_ATLEAST(m,n,p) (((m<<16) + (n<<8) + p) <= ((LIBZIP_VERSION_MAJOR<<16) + (LIBZIP_VERSION_MINOR<<8) + LIBZIP_VERSION_MICRO))

#endif	/* PHP_ZIP_H */

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
