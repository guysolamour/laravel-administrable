#!/bin/bash
###################################################################################################################
##########                                                                                            #############
##########                                  DEPLOYMENT SYSTEM                                         #############
##########                        The technology used is ansible with ansistrano  roles               #############
##########        by Guy-roland ASSALE | rolandassale@gmail.com | https://www.roland-assale.info      #############
###################################################################################################################


###################################################################################################################
##########                                                                                            #############
##########                                 PREPARING THE SERVER                                       #############
##########            1- Create a user to run the tasks                                               #############
##########                  sudo useradd user -s /bin/bash -d /home/user -m -G sudo                   #############
##########                  > add password user user@000.000.000.000                                  #############
##########                  > add ssh key with sshcopyid                                              #############
##########                  ssh-copy-id -i ~/.ssh/id_rsa.pub user@000.000.000.000                     #############
##########                  > add user to sudoers file                                                #############
##########                  run visudo  and append user ALL=(ALL) NOPASSWD:ALL at the end of the line #############
##########                  sudo apt install -y python-apt                                            #############
##########            2- Put this user in the sodoers files and add SSH keys in the remote            #############
##########            3- install python-apt on the remote machine                                     #############
###################################################################################################################


###################################################################################################################
##########                                                                                            #############
##########                            THE DIFFERENT STEPS                                             #############
##########        1- Generate the file that will contain the passwords                                #############
##########        > make create                                                                       #############
##########          Enter the password that will be used for decryption                               #############
##########          This password must be saved in clear in the .vault-pass file                      #############
##########          This file must not be versioned.                                                  #############
##########                                                                                            #############
##########        2- To modify the file containing the passwords                                      #############
##########        > make edit                                                                         #############
##########                                                                                            #############
##########        3- To view the contents of the file                                                 #############
##########        > make view                                                                         #############
##########                                                                                            #############
##########        NB:  The variables (passwords) to be entered                                        #############
##########             vault_user_password: password                                                  #############
##########             vault_database_password: password                                              #############
##########             vault_database_root_password: password                                         #############
##########             vault_ftp_password: password                                                   #############
##########             vault_admin_local_password: password                                           #############
##########             vault_admin_production_password: password                                      #############
##########             vault_ftp_password: password                                                   #############
##########   vault_app_key: Copy and paste the output of php artisan key:generate --show command      #############                               #############
###################################################################################################################


# The playbook folder
PLAYBOOKS_DIR=$(pwd)/vendor/guysolamour/laravel-administrable/deployment

# Le dossier où sera stocké les fichiers temporaires et les fichiers de mot de passe.
# Ce dossier ne doit pas etre versionné
TEMPORARY_DIR=$(pwd)/.deployment

# The name of the file that will store the code to decrypt passwords
VAULT_PASS=.vault-pass


# User who will be used to launch the tasks on the server
# This one must be able to connect in SSH on the remote machine without password
# Default value is root
REMOTE_DEFAULT_USER=$(whoami)


# The user who will run the server in PHP
# In most cases the value is www-data (nginx && apache)
REMOTE_SERVER_USER=www-data


# Ip address for the remote server
HOST=


# The strategy used to get the source files (git clone or git archive)
# Two strategies are available: clone or archive
COPY_STRATEGY=clone # or archive

# The url of the repository to use if the copy_strategy is clone
# It is not used if the copy_strategy is archived
REPOSITORY=


# The project name
APPLICATION={{appname}}



# The website domain
DOMAIN={{appurl}}


# The name of the archive that will be created when copying the source code
# This value should not be changed
ARCHIVE=${APPLICATION}


# The user to create
USER=${APPLICATION}


# The database to create
DATABASE_NAME=${APPLICATION}


# The database user
DATABASE_USER=${APPLICATION}


# The default domain of the server
DEFAULT_NGINX_SERVEUR=${DOMAIN}


# The version of NODE JS (lts) to install
NODEJS_VERSION=12

# The PHP version to install
PHP_VERSION=8.0


# The number of releases to keep
KEEP_RELEASES=5

# Build javascript and css files with NPM
BUILD_JAVASCRIPT=no


# Add cron job scheduler
SCHEDULER=yes

# Install laravel horizon to manage queues
HORIZON=no

# Le mail qui sera utilisé pour redirigére tous les mails envoyer à l'utilisateur root
# No ou false pour désactiver  ou le mail pour envoyer
FORWARD_ROOT_EMAILS=no

###################################################################################################################
##########                                                                                            #############
##########                                 THE ENV FILE                                               #############
##########               You can also modify this  env.j2 template in the playbook folder             #############
###################################################################################################################


# For Administrable package
APP_FIRST_NAME={{appfirstname}}
APP_LAST_NAME={{applastname}}


# For Administrable package
FTP_HOST={{ftphost}}
FTP_USERNAME={{ftpusername}}

# The email used to send notifications
MAIL_FROM_ADDRESS={{notifemail}}

# For Administrable package
MODEL_CACHE_ENABLED=true


###################################################################################################################
##########                                                                                            #############
##########                             DUMPS (BACKUP)                                                 #############
##########                                                                                            #############
###################################################################################################################

# The path of the database dump file
DB_DUMP_PATH=${pwd}


# The path of the storage folder dump file
STORAGE_DUMP_PATH=$(pwd)


###################################################################################################################
##########                                                                                            #############
##########                               DKIM KEYS                                                    #############
##########                                                                                            #############
###################################################################################################################


# Generate DKIM keys for email encryption
DKIM_KEYS=yes

# In the ansistrano shared folder
DKIM_STORAGE_PRIVATE_KEY_PATH=storage/dkim/dkim.private.key
DKIM_STORAGE_PUBLIC_KEY_PATH=storage/dkim/dkim.public.key

# Display the public key in console in order to copy it and put it on the host of the domain
SHOW_DKIM_PUBLIC_KEY=yes

###################################################################################################################
##########                                                                                            #############
##########                               OHMYZSH                                                      #############
##########                                                                                            #############
###################################################################################################################


ZSH_THEME=random
ZSH_PLUGINS="git composer npm sudo"

###################################################################################################################
##########                                                                                            #############
##########                               VIM                                                          #############
##########                                                                                            #############
###################################################################################################################

# vimrc path to copy
VIM_RC_URL=https://raw.githubusercontent.com/amix/vimrc/master/vimrcs/basic.vim

VIM_SET_NUMBER=yes


###################################################################################################################
##########                                                                                            #############
##########                               PHP                                                          #############
##########                                                                                            #############
###################################################################################################################

MEMORY_LIMIT=128M
UPLOAD_MAX_FILESIZE=50M
POST_MAX_SIZE=50M

# This line can not be removed
source ${PLAYBOOKS_DIR}/tasks.sh
