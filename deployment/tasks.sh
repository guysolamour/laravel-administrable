#!/bin/bash

ALL_COMMANDS=(
  'help' 'run' 'exec' 'dkim' 'rollback' 'db:dump' 'db:deploy' 'db:import' 'clean' 'storage:deploy'
  'install' 'storage:dump' 'storage:import' 'db:seed' 'password:create' 'password:view' 'password:edit'
)


#------------------------------------------------------------------------------------------------------------------------------>
# We get first argument, the name of the command to run and lowercase it
# if the command is not available, we throw and exception and stop script
# if no value is provided, the help command will be run
#------------------------------------------------------------------------------------------------------------------------------>
if [ $1 ]
then
    if [[ " ${ALL_COMMANDS[*]} " =~ " ${1,,} " ]]; then
        TARGET=${1,,}
    else
        echo ${1,,} 'is not valid. Available commands ares' ${ALL_COMMANDS[*]}
        exit 1
    fi
else
    TARGET=help
fi

#------------------------------------------------------------------------------------------------------------------------------>
# We get second argument, for some commands
#------------------------------------------------------------------------------------------------------------------------------>
if [ $2 ]
then
    ARGUMENT=${2}
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
#  Display this help (all commands and descriptions)
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "help" ]
then
    echo '----------------------------------------------------------------------------------------------------------->'
    echo "Welcome to administrable deployer!!!"
    echo "Available ${#ALL_COMMANDS[@]} commands are:"
    for item in ${ALL_COMMANDS[@]}; do
      echo '---> bash deploy.sh' $item
    done
    echo '----------------------------------------------------------------------------------------------------------->'
    exit 1
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Install and configure the server before deploying website
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "install" ]
then
    ansible-playbook -i "${HOST}," ${PLAYBOOKS_DIR}/tasks/install.yml --vault-password-file ${VAULT_PASS}  \
            --extra-vars " \n
             	playbooks_directory_path='${PLAYBOOKS_DIR}'  zsh_plugins='${ZSH_PLUGINS}'
                application='${APPLICATION}' domain='${DOMAIN}' php_version='${PHP_VERSION}'
                remote_server_user='${REMOTE_SERVER_USER}' nodejs_version='${NODEJS_VERSION}'
                remote_default_user='${REMOTE_DEFAULT_USER}' domain='${DOMAIN}'
                user='${USER}' database_name='${DATABASE_NAME}' zsh_theme='${ZSH_THEME}'
                database_user='${DATABASE_USER}' default_nginx_serveur='${DEFAULT_NGINX_SERVEUR}'
                ftp_host='${FTP_HOST}' ftp_username='${FTP_USERNAME}' vim_rc_url='${VIM_RC_URL}'
                model_cache_enabled='${MODEL_CACHE_ENABLED}' vim_set_number='${VIM_SET_NUMBER}'
                mail_from_address='${MAIL_FROM_ADDRESS}' app_first_name='${APP_FIRST_NAME}'
                app_last_name='${APP_LAST_NAME}' memory_limit='${MEMORY_LIMIT}'
                upload_max_filesize='${UPLOAD_MAX_FILESIZE}' post_max_size='${POST_MAX_SIZE}'
                forward_root_emails='${FORWARD_ROOT_EMAILS}' temporary_dir='${TEMPORARY_DIR}'
            "
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Deploy website
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "run" ]
then
    ansible-playbook -i "${HOST}," ${PLAYBOOKS_DIR}/tasks/deploy.yml --vault-password-file ${VAULT_PASS} \
            --extra-vars " \n
                playbooks_directory_path='${PLAYBOOKS_DIR}' show_dkim_public_key='${SHOW_DKIM_PUBLIC_KEY}'
                application='${APPLICATION}'  domain='${DOMAIN}' php_version='${PHP_VERSION}'
                remote_default_user='${REMOTE_DEFAULT_USER}' remote_server_user='${REMOTE_SERVER_USER}'
                archive='${ARCHIVE}' repository='${REPOSITORY}' temporary_dir='${TEMPORARY_DIR}'
                user='${USER}' database_name='${DATABASE_NAME}'
                dkim_storage_private_key_path='${DKIM_STORAGE_PRIVATE_KEY_PATH}'
                dkim_storage_public_key_path='${DKIM_STORAGE_PUBLIC_KEY_PATH}'
                database_user='${DATABASE_USER}' keep_releases='${KEEP_RELEASES}'
                build_javascript='${BUILD_JAVASCRIPT}' copy_strategy='${COPY_STRATEGY}'
                scheduler='${SCHEDULER}' horizon='${HORIZON}' dkim_keys='${DKIM_KEYS}'
            " -v
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Rollback to previous website version
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "rollback" ]
then
    ansible-playbook -i "${HOST}," ${PLAYBOOKS_DIR}/tasks/rollback.yml --vault-password-file ${VAULT_PASS}  \
            --extra-vars " \n
               	playbooks_directory_path='${PLAYBOOKS_DIR}' show_dkim_public_key='${SHOW_DKIM_PUBLIC_KEY}'
                application='${APPLICATION}' domain='${DOMAIN}' php_version='${PHP_VERSION}'
                remote_default_user='${REMOTE_DEFAULT_USER}' temporary_dir='${TEMPORARY_DIR}'
                remote_server_user='${REMOTE_SERVER_USER}'
                archive='${ARCHIVE}' repository='${REPOSITORY}'
                user='${USER}' database_name='${DATABASE_NAME}'
                database_user='${DATABASE_USER}' keep_releases='${KEEP_RELEASES}'
                build_javascript='${BUILD_JAVASCRIPT}' copy_strategy='${COPY_STRATEGY}'
                scheduler='${SCHEDULER}' horizon='${HORIZON}' dkim_keys='${DKIM_KEYS}'
            "
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Seed production database
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "db:seed" ]
then
    ansible-playbook -i "${HOST}," ${PLAYBOOKS_DIR}/tasks/dbseed.yml --vault-password-file ${VAULT_PASS}  \
            --extra-vars " \n
            	seed_file='${ARGUMENT}' user='${USER}'
              domain='${DOMAIN}'
            "
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Dump production database (sql file) and move it to host current playbook directory
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "db:dump" ]
then
    ansible-playbook -i "${HOST}," ${PLAYBOOKS_DIR}/tasks/dbdump.yml --vault-password-file ${VAULT_PASS}  \
            --extra-vars " \n
              playbooks_directory_path='${PLAYBOOKS_DIR}' temporary_dir='${TEMPORARY_DIR}'
              application='${APPLICATION}' domain='${DOMAIN}'
              user='${USER}' database_name='${DATABASE_NAME}'
              database_user='${DATABASE_USER}' path='${DB_DUMP_PATH}'
            "
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Sync local and production environnements database
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "db:deploy" ]
then
	  ansible-playbook -i "${HOST}," ${PLAYBOOKS_DIR}/tasks/dbdeploy.yml --vault-password-file ${VAULT_PASS} \
            --extra-vars " \n
                  playbooks_directory_path='${PLAYBOOKS_DIR}' temporary_dir='${TEMPORARY_DIR}'
                  user='${USER}' remote_default_user='${REMOTE_DEFAULT_USER}'
                  application='${APPLICATION}' domain='${DOMAIN}'
            "
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Sync production and local environnements database
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "db:import" ]
then
	  ansible-playbook -i "${HOST}," ${PLAYBOOKS_DIR}/tasks/dbimport.yml --vault-password-file ${VAULT_PASS} \
            --extra-vars " \n
                  playbooks_directory_path='${PLAYBOOKS_DIR}' temporary_dir='${TEMPORARY_DIR}'
                  user='${USER}' remote_default_user='${REMOTE_DEFAULT_USER}'
                  application='${APPLICATION}' domain='${DOMAIN}'
            "
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Dump production storage folder (zip file) and move it to host current playbook directory
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "storage:dump" ]
then
    ansible-playbook -i "${HOST}," ${PLAYBOOKS_DIR}/tasks/storagedump.yml --vault-password-file ${VAULT_PASS}  \
            --extra-vars " \n
            	playbooks_directory_path='${PLAYBOOKS_DIR}' temporary_dir='${TEMPORARY_DIR}'
              application='${APPLICATION}' domain='${DOMAIN}'
              user='${USER}' temporary_dir='${TEMPORARY_DIR}'
              path='${STORAGE_DUMP_PATH}'
            "
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Sync production  and local storage folder
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "storage:import" ]
then
    ansible-playbook -i "${HOST}," ${PLAYBOOKS_DIR}/tasks/storageimport.yml --vault-password-file ${VAULT_PASS}  \
            --extra-vars " \n
                playbooks_directory_path='${PLAYBOOKS_DIR}' temporary_dir='${TEMPORARY_DIR}'
                user='${USER}' remote_default_user='${REMOTE_DEFAULT_USER}'
                application='${APPLICATION}' domain='${DOMAIN}'
                storage_dump_path='${STORAGE_DUMP_PATH}'
            " -v
fi
#------------------------------------------------------------------------------------------------------------------------------>

#------------------------------------------------------------------------------------------------------------------------------>
# Sync production  and local storage folder
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "storage:deploy" ]
then
    ansible-playbook -i "${HOST}," ${PLAYBOOKS_DIR}/tasks/storagedeploy.yml --vault-password-file ${VAULT_PASS}  \
            --extra-vars " \n
                playbooks_directory_path='${PLAYBOOKS_DIR}' temporary_dir='${TEMPORARY_DIR}'
                user='${USER}' remote_default_user='${REMOTE_DEFAULT_USER}'
                application='${APPLICATION}' domain='${DOMAIN}'
                storage_dump_path='${STORAGE_DUMP_PATH}' remote_server_user='${REMOTE_SERVER_USER}
            " -v
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Run a command on host machine from deploy directory
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "exec" ]
then
    ssh ${APPLICATION}@${HOST} "cd /home/${APPLICATION}/${DOMAIN}/current && ${ARGUMENT}"
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Display dkim publick key
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "dkim" ]
then
    ssh ${APPLICATION}@${HOST} "cd /home/${APPLICATION}/${DOMAIN}/current && cat ${DKIM_STORAGE_PUBLIC_KEY_PATH}"
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Remove deploy temporary files
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "clean" ]
then
	  rm -rf ${TEMPORARY_DIR}/tmp/${ARCHIVE}
	  rm -rf ${TEMPORARY_DIR}/tmp/${ARCHIVE}.tgz
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Create passwords file
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "password:create" ]
then
    ansible-vault create ${TEMPORARY_DIR}/variables/passwords.yml --vault-password-file=${VAULT_PASS}
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# View passwords file
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "password:view" ]
then
    ansible-vault view ${TEMPORARY_DIR}/variables/passwords.yml --vault-password-file=${VAULT_PASS}
fi
#------------------------------------------------------------------------------------------------------------------------------>


#------------------------------------------------------------------------------------------------------------------------------>
# Edit passwords file
#------------------------------------------------------------------------------------------------------------------------------>
if [ $TARGET == "password:edit" ]
then
    ansible-vault edit ${TEMPORARY_DIR}/variables/passwords.yml --vault-password-file=${VAULT_PASS}
fi
#------------------------------------------------------------------------------------------------------------------------------>
