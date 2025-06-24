files:
	@read -p "Enter the name: " name; \
	read -p "Enter the controller path: " path; \
	read -p "Enter the controller type: " type; \
	read -p "Do you want to create controller class? (y/n): " create_controller; \
	read -p "Do you want to create model classes? (y/n): " create_model; \
	read -p "Do you want to create dto classes? (y/n): " create_dto; \
	read -p "Do you want to create action classes? (y/n): " create_action; \
	read -p "Do you want to create resource class? (y/n): " create_resource; \
	read -p "Do you want to create collection class? (y/n): " create_collection; \
	if [ "$$create_model" = "y" ]; then \
		php artisan make:model $$name -m; \
	fi; \
	if [ "$$create_controller" = "y" ]; then \
		php artisan make:controller $$path/$$name"Controller" --$$type; \
	fi; \
	if [ "$$create_dto" = "y" ]; then \
		php artisan make:dto Create$$name"Dto"; \
		php artisan make:dto Update$$name"Dto"; \
	fi; \
	if [ "$$create_action" = "y" ]; then \
		php artisan make:class Actions/$$name/"Create"$$name"Action"; \
		php artisan make:class Actions/$$name/"Update"$$name"Action"; \
	fi; \
	if [ "$$create_resource" = "y" ]; then \
		php artisan make:resource $$name"Resource"; \
	fi; \
	if [ "$$create_collection" = "y" ]; then \
		php artisan make:resource $$name"Collection" --collection; \
	fi;