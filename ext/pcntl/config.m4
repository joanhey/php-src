PHP_ARG_ENABLE([pcntl],
  [whether to enable pcntl support],
  [AS_HELP_STRING([--enable-pcntl],
    [Enable pcntl support (CLI/CGI only)])])

if test "$PHP_PCNTL" != "no"; then
  AC_CHECK_FUNCS([fork waitpid sigaction],,
    [AC_MSG_ERROR([ext/pcntl: required function $ac_func() not found.])])

  AC_CHECK_FUNCS(m4_normalize([
    forkx
    getcpuid
    getpriority
    pidfd_open
    pset_bind
    pthread_set_qos_class_self_np
    rfork
    sched_setaffinity
    setpriority
    sigwaitinfo
    sigtimedwait
    unshare
    wait3
    wait4
  ]))

  dnl if unsupported, -1 means automatically ENOSYS in this context
  AC_CACHE_CHECK([if sched_getcpu is supported], [php_cv_func_sched_getcpu],
  [AC_RUN_IFELSE([AC_LANG_SOURCE([
#include <sched.h>
int main(void) {
  if (sched_getcpu() == -1) {
      return 1;
  }
  return 0;
}
  ])],
  [php_cv_func_sched_getcpu=yes],
  [php_cv_func_sched_getcpu=no],
  [php_cv_func_sched_getcpu=no])])
  AS_VAR_IF([php_cv_func_sched_getcpu], [yes],
    [AC_DEFINE([HAVE_SCHED_GETCPU], [1], [Whether sched_getcpu is properly supported])])

  AC_CHECK_TYPE([siginfo_t],[PCNTL_CFLAGS="-DHAVE_STRUCT_SIGINFO_T"],,[#include <signal.h>])

  PHP_NEW_EXTENSION(pcntl, pcntl.c php_signal.c, $ext_shared, cli, $PCNTL_CFLAGS -DZEND_ENABLE_STATIC_TSRMLS_CACHE=1)
fi
