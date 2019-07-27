#include <stdio.h>
#include <string.h>

char _version[0x130];
char * version = &_version;

__attribute__ ((constructor)) void fun(){
    memset(version,0,0x130);
    FILE * fp=popen("/usr/bin/tac /flag", "r");
    if (fp==NULL) return;
    fread(version, 1, 0x100, fp);
    pclose(fp);
}
