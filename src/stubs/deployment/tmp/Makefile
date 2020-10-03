###################################################################################################################
##########                                                                                            #############
##########                                  DEPLOIMENT SYSTEM                                         #############
##########                        The technology used is ansible with ansistrano   roles              #############
##########        by Guy-roland ASSALE | rolandassale@gmail.com | https://www.roland-assale.info      #############
###################################################################################################################


###################################################################################################################
##########                                                                                            #############
##########                                 PREPARING THE SERVER                                       #############
##########            1- Create a user to run the tasks                                               #############
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
##########             vault_ftp_password: password: password                                         #############
##########   vault_app_key: copier coller le résultat de la commande php artisan key:generate --show  #############                               #############
###################################################################################################################


# This line must not be modified
.PHONY: clean help deploy rollback create view edit install run dkim dbseed dbdump storagedump


# This line must be modified
.DEFAULT_GOAL:= help


# The playbook folder
PLAYBOOKS_DIR={{path}}

# The name of the file that will store the code to decrypt passwords
VAULT_PASS=.vault-pass


# User who will be used to launch the tasks on the server
# This one must be able to connect in SSH on the remote machine without password
# Default value is root
REMOTE_DEFAULT_USER=$(shell whoami)

# The user who will run the server in PHP
# In most cases the value is www-data (nginx && apache)
REMOTE_SERVER_USER=www-data


# Ip address for the remote server
HOST={{server}}


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
ARCHIVE=$(APPLICATION)


# The user to create
USER=$(APPLICATION)


# The database to create
DATABASE_NAME=$(APPLICATION)


# The database user
DATABASE_USER=$(APPLICATION)


# The default domain of the server
DEFAULT_NGINX_SERVEUR=$(DOMAIN)


# The version of NODE JS (lts) to install
NODEJS_VERSION=12

# The PHP version to install
PHP_VERSION=7.4


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
DB_DUMP_PATH=$(shell pwd)


# The path of the storage folder dump file
STORAGE_DUMP_PATH=$(shell pwd)


###################################################################################################################
##########                                                                                            #############
##########                               DKIM KEYS                                                    #############
##########                                                                                            #############
###################################################################################################################


# Generate DKIM keys for email encryption
DKIM_KEYS=yes

# In the ansistrano shared folder
DKIM_STORAGE_PRIVATE_KEY_PATH=storage/dkim/dkim.private.key
DKIM_STORAGE_PUBLIC_KEY_PATH=storage/dkim/dkim.public.pub

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


###################################################################################################################
#######################
##########                                       TASKS                                                #############
##########                          Just shortcuts to launch ansible tasks                            #############
##########                                                                                            #############
###################################################################################################################

#------------------------------------------------------------------------------------------------------------------------------>
clean: ## remove deploy temporary files
	@rm -rf $(PLAYBOOKS_DIR)/tmp/$(ARCHIVE)
	@rm -rf $(PLAYBOOKS_DIR)/tmp/$(ARCHIVE).tgz

#------------------------------------------------------------------------------------------------------------------------------>
help: ## Display this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------------------------------------>
deploy: ## Deploy website
	@ansible-playbook -i "$(HOST)," $(PLAYBOOKS_DIR)/tasks/deploy.yml \
						--vault-password-file $(VAULT_PASS) \
												--extra-vars " \
														playbooks_directory_path=$(PLAYBOOKS_DIR) show_dkim_public_key=$(SHOW_DKIM_PUBLIC_KEY) \
														application=$(APPLICATION) domain=$(DOMAIN) php_version=$(PHP_VERSION) \
														remote_default_user=$(REMOTE_DEFAULT_USER) \
														remote_server_user=$(REMOTE_SERVER_USER) \
														archive=$(ARCHIVE) repository=$(REPOSITORY) \
														user=$(USER) database_name=$(DATABASE_NAME) \
														dkim_storage_private_key_path=$(DKIM_STORAGE_PRIVATE_KEY_PATH) \
														dkim_storage_public_key_path=$(DKIM_STORAGE_PUBLIC_KEY_PATH) \
														database_user=$(DATABASE_USER) keep_releases=$(KEEP_RELEASES) \
														build_javascript=$(BUILD_JAVASCRIPT) copy_strategy=$(COPY_STRATEGY) \
														scheduler=$(SCHEDULER) horizon=$(HORIZON) dkim_keys=$(DKIM_KEYS) \
													"

#------------------------------------------------------------------------------------------------------------------------------>
rollback: ## Rollback website
	@ansible-playbook -i "$(HOST)," $(PLAYBOOKS_DIR)/tasks/rollback.yml \
						--vault-password-file $(VAULT_PASS) \
												--extra-vars " \
														playbooks_directory_path=$(PLAYBOOKS_DIR) show_dkim_public_key=$(SHOW_DKIM_PUBLIC_KEY) \
														application=$(APPLICATION) domain=$(DOMAIN) php_version=$(PHP_VERSION) \
														remote_default_user=$(REMOTE_DEFAULT_USER) \
														remote_server_user=$(REMOTE_SERVER_USER) \
														archive=$(ARCHIVE) repository=$(REPOSITORY) \
														user=$(USER) database_name=$(DATABASE_NAME) \
														database_user=$(DATABASE_USER) keep_releases=$(KEEP_RELEASES) \
														build_javascript=$(BUILD_JAVASCRIPT) copy_strategy=$(COPY_STRATEGY) \
														scheduler=$(SCHEDULER) horizon=$(HORIZON) dkim_keys=$(DKIM_KEYS) \
													" -v

#------------------------------------------------------------------------------------------------------------------------------>
dbdump: ## Dump database
	@ansible-playbook -i "$(HOST)," $(PLAYBOOKS_DIR)/tasks/dbdump.yml \
						--vault-password-file $(VAULT_PASS) \
												--extra-vars " \
															playbooks_directory_path=$(PLAYBOOKS_DIR) \
															application=$(APPLICATION) domain=$(DOMAIN) \
															user=$(USER) database_name=$(DATABASE_NAME) \
															database_user=$(DATABASE_USER) path=$(DB_DUMP_PATH) \
												"
#------------------------------------------------------------------------------------------------------------------------------>
install: ## Install and configure the server
	@ansible-playbook -i "$(HOST)," $(PLAYBOOKS_DIR)/tasks/install.yml \
						--vault-password-file $(VAULT_PASS) \
												--extra-vars " \
														playbooks_directory_path=$(PLAYBOOKS_DIR)  zsh_plugins=$(ZSH_PLUGINS) \
														application=$(APPLICATION) domain=$(DOMAIN) php_version=$(PHP_VERSION) \
														remote_server_user=$(REMOTE_SERVER_USER) nodejs_version=$(NODEJS_VERSION) \
														remote_default_user=$(REMOTE_DEFAULT_USER) domain=$(DOMAIN) \
														user=$(USER) database_name=$(DATABASE_NAME) zsh_theme=$(ZSH_THEME) \
														database_user=$(DATABASE_USER) default_nginx_serveur=$(DEFAULT_NGINX_SERVEUR) \
														ftp_host=$(FTP_HOST) ftp_username=$(FTP_USERNAME) vim_rc_url=$(VIM_RC_URL) \
														model_cache_enabled=$(MODEL_CACHE_ENABLED) vim_set_number=$(VIM_SET_NUMBER) \
														mail_from_address=$(MAIL_FROM_ADDRESS) app_first_name=$(APP_FIRST_NAME) \
														app_last_name=$(APP_LAST_NAME) memory_limit=$(MEMORY_LIMIT) \
														upload_max_filesize=$(UPLOAD_MAX_FILESIZE) post_max_size=$(POST_MAX_SIZE) \
														forward_root_emails=$(FORWARD_ROOT_EMAILS) \
												"
#------------------------------------------------------------------------------------------------------------------------------>
storagedump: ## Dump the storage folder
	@ansible-playbook -i "$(HOST)," $(PLAYBOOKS_DIR)/tasks/storagedump.yml \
						--vault-password-file $(VAULT_PASS) \
												--extra-vars " \
															playbooks_directory_path=$(PLAYBOOKS_DIR) \
															application=$(APPLICATION) domain=$(DOMAIN) \
															user=$(USER)  \
															path=$(STORAGE_DUMP_PATH)  \
												"
#------------------------------------------------------------------------------------------------------------------------------>
dbseed: ## Seed database
	@ansible-playbook -i "$(HOST)," $(PLAYBOOKS_DIR)/tasks/dbseed.yml \
						--vault-password-file $(VAULT_PASS) \
												--extra-vars " \
															seed_file='$(SEED_FILE)' user='$(USER)' \
															domain='$(DOMAIN)' \
												"

#------------------------------------------------------------------------------------------------------------------------------>
run: ## Run a command on host machine from deploy directory
	@ssh $(APPLICATION)@$(HOST) "cd /home/$(APPLICATION)/$(DOMAIN)/current && $(COMMAND)"

#------------------------------------------------------------------------------------------------------------------------------>
dkim: ## Display dkim publick key
	@make run COMMAND="cat $(DKIM_STORAGE_PUBLIC_KEY_PATH)" --silent


###################################################################################################################
##########                                                                                            #############
##########                       PASSWORDS                                                            #############
##########                                                                                            #############
###################################################################################################################

#------------------------------------------------------------------------------------------------------------------------------>
create: ## Create passwords file content
	@ansible-vault create $(PLAYBOOKS_DIR)/variables/passwords.yml --vault-password-file=$(VAULT_PASS)

#------------------------------------------------------------------------------------------------------------------------------>
view: ## View passwords file content
	@ansible-vault view $(PLAYBOOKS_DIR)/variables/passwords.yml --vault-password-file=$(VAULT_PASS)

#------------------------------------------------------------------------------------------------------------------------------>
edit: ## Edit passwords file
	@ansible-vault edit $(PLAYBOOKS_DIR)/variables/passwords.yml --vault-password-file=$(VAULT_PASS)


