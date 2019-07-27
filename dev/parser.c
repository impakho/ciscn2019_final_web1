#include <stddef.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <dirent.h>
#include <id3v2lib.h>

int frame_size;
int * frame_data;
int * passwd;
char _frame_data[0x100];
char _passwd[0x130];

void init_proc(){
    frame_size=0;
    frame_data=&_frame_data;
    memset(frame_data,0,0x100);
    passwd=&_passwd;
    memset(passwd,0,0x130);
}

int check_password(char * password){
    char filename[0x130];
    struct dirent ** filelist;
    int n = scandir("config/", &filelist, 0, alphasort);
    if (n < 0){
        return 0;
    }else{
        int match=0;
        while(n--){
            char * res;
            res = strrchr(filelist[n]->d_name, '.');
            if (res!=NULL) {
                if (strcmp(res,".passwd")==0){
                    match=1;
                    memset(&filename,0,0x130);
                    strcpy(&filename,"config/");
                    strcat(&filename,filelist[n]->d_name);
                }
            }
            free(filelist[n]);
        }
        free(filelist);
        if (match==0) return 0;
    }
    FILE * fp=fopen(&filename,"r");
    if (fp == NULL) return 0;
    fread(passwd,1,0x100,fp);
    fclose(fp);
    if (strcmp(passwd,password) == 0) return 1;
    return 0;
}

void read_title(char * filename){
    ID3v2_tag * tag = load_tag(filename);
    if (tag != NULL){
        ID3v2_frame* title_frame = tag_get_title(tag);
        ID3v2_frame_text_content * title_content = parse_text_frame_content(title_frame);
        frame_size=title_content->size;
        memcpy(frame_data,title_content->data,frame_size%0x100);
    }
}

void read_artist(char * filename){
    ID3v2_tag * tag = load_tag(filename);
    if (tag != NULL){
        ID3v2_frame* artist_frame = tag_get_artist(tag);
        ID3v2_frame_text_content * artist_content = parse_text_frame_content(artist_frame);
        frame_size=artist_content->size;
        memcpy(frame_data,artist_content->data,frame_size%0x100);
    }
}

void read_album(char * filename){
    ID3v2_tag * tag = load_tag(filename);
    if (tag != NULL){
        ID3v2_frame* album_frame = tag_get_album(tag);
        ID3v2_frame_text_content * album_content = parse_text_frame_content(album_frame);
        frame_size=album_content->size;
        memcpy(frame_data,album_content->data,frame_size%0x100);
    }
}

int * parse(char * password, char * classname, char * filename){
    init_proc();
    if (check_password(password)==1){
        if (strcmp(classname,"title")==0){
            read_title(filename);
        }else if (strcmp(classname,"artist")==0){
            read_artist(filename);
        }else if (strcmp(classname,"album")==0){
            read_album(filename);
        }
    }
    return &frame_size;
}
