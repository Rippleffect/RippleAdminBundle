RippleAdminBundle
==================

The Rippleffect Admin Bundle provides common functionality for the use of SonataAdminBundle, SonataUserBundle, SonataBlockBundle
and others to create an out-of-the-box admin solution.

The installation of this bundle will install the following bundles:

  * SonataAdminBundle
  * SonataBlockBundle
  * SonatajQueryBundle
  * SonataAdminBundle
  * SonataDoctrineORMAdminBundle
  * KnpMenuBundle
  * RippleUserBundle
  * FOSUserBundle

This walkthrough covers specifics for the `RippleUserBundle`, and assumes the reader has knowledge of (or is at least going
 to cover the content of) the [SonataAdminBundle documentation][1]


Installing the bundle
----------------------

The bundle is installed via composer. Add the following to your `composer.json` file:

    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/Rippleffect/RippleAdminBundle"
        }
    ]

And then add the package as a dependency:

    "rippleffect/admin-bundle": "dev-master"

Now you can update your dependencies as normal using `composer update`.

Your `app/config/security.yml` configuration should look something like below (with two separate firewalls for "admin"
and "main". The admin firewall is for restricting access to the backend, and the main firewall is your default frontend
firewall which should largely remain untouched.

    jms_security_extra:
        secure_all_services: false
        expressions: true

    security:
        acl:
            connection: default
        encoders:
            "FOS\UserBundle\Model\UserInterface":
                algorithm: sha512
                encode_as_base64: true
                iterations: 5

        role_hierarchy:
            ROLE_ADMIN:       [ROLE_USER, ROLE_SONATA_ADMIN]
            ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]

        providers:
            fos_userbundle:
                id: ripple_user.user_provider

        firewalls:
            # this is the "frontend" firewall
            main:
                pattern: ^/
                form_login:
                    provider: fos_userbundle
                    csrf_provider: form.csrf_provider
                    default_target_path: /
                    always_use_default_target_path: true
                logout:       true
                anonymous:    true
            # this is the administration area firewall
            admin:
                anonymous: true
                pattern: ^/admin(.*)
                switch_user: true
                context: user
                form_login:
                    provider: fos_userbundle
                    login_path:     /admin/login
                    use_forward:    false
                    check_path:     /admin/login_check
                    failure_path:   null
                    use_referer:    true
                logout:
                    path:           /logout
                    target:         welcome

        access_control:
            # admin related routes
            - { path: ^/admin/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/logout$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/login-check$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin, role: [ROLE_ADMIN, ROLE_SONATA_ADMIN] }

            #this is for the RippleUserBundle invite system
            - { path: ^/users/invite, role: IS_AUTHENTICATED_ANONYMOUSLY }
            #any public assets here
            - { path: ^/css|js|images, role: IS_AUTHENTICATED_ANONYMOUSLY }

            # authentication related routes
            - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/register, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }

            # your main application path - set this to IS_AUTHENTICATED_ANONYMOUSLY if your app doesn't require login
            - { path: ^/, role: ROLE_USER }

We also need to import routes into the project, so your `app/config/routing.yml` should include the following:

    ripple_user:
        resource: "@RippleUserBundle/Resources/config/routing.yml"
        prefix: /users

    fos_user_security:
        resource: "@FOSUserBundle/Resources/config/routing/security.xml"

    fos_user_profile:
        resource: "@FOSUserBundle/Resources/config/routing/profile.xml"
        prefix: /profile

    fos_user_register:
        resource: "@FOSUserBundle/Resources/config/routing/registration.xml"
        prefix: /register

    fos_user_resetting:
        resource: "@FOSUserBundle/Resources/config/routing/resetting.xml"
        prefix: /resetting

    fos_user_change_password:
        resource: "@FOSUserBundle/Resources/config/routing/change_password.xml"
        prefix: /profile

    admin:
        resource: '@SonataAdminBundle/Resources/config/routing/sonata_admin.xml'
        prefix: /admin

    admin_security:
        resource: '@SonataUserBundle/Resources/config/routing/admin_security.xml'
        prefix: /admin

    _sonata_admin:
        resource: .
        type: sonata_admin
        prefix: /admin

Our `app/config/config.yml` will also need some extra configuration adding for `FOSUserBundle`:

    fos_user:
        db_driver:     orm
        firewall_name: main
        user_class:   Ripple\UserBundle\Entity\User #change this to your application User entity
        profile:
            form:
                type: ripple_user_profile
        group:
            group_class: Ripple\UserBundle\Entity\Group #change this to your application Group entity

Next, add the following in `app/config/admin.yml`. Create the file if it does not already exist and add the import to your
`app/config/config.yml`:

    imports:
        - { resource: admin.yml }

The `app/config/admin.yml` content:

    # SonataAdminBundle Configuration
    sonata_admin:
        title: Project Title
        title_logo: /path/to/your/logo.png

        templates:
            # default global templates
            layout:  RippledminBundle::standard_layout.html.twig
            ajax:    SonataAdminBundle::ajax_layout.html.twig
            dashboard: SonataAdminBundle:Core:dashboard.html.twig

            # default actions templates, should extend a global templates
            list:    SonataAdminBundle:CRUD:list.html.twig
            show:    SonataAdminBundle:CRUD:show.html.twig
            edit:    SonataAdminBundle:CRUD:edit.html.twig

            # additional template blocks
            user_block:               MyFoodAdminBundle:Sonata:user_block.html.twig
            preview:                  SonataAdminBundle:CRUD:preview.html.twig
            history:                  SonataAdminBundle:CRUD:history.html.twig
            history_revision:         SonataAdminBundle:CRUD:history_revision.html.twig
            action:                   SonataAdminBundle:CRUD:action.html.twig
            list_block:               SonataAdminBundle:Block:block_admin_list.html.twig
            short_object_description: SonataAdminBundle:Helper:short-object-description.html.twig
            delete:                   SonataAdminBundle:CRUD:delete.html.twig
            batch:                    SonataAdminBundle:CRUD:list__batch.html.twig
            batch_confirmation:       SonataAdminBundle:CRUD:batch_confirmation.html.twig
            inner_list_row:           SonataAdminBundle:CRUD:list_inner_row.html.twig
            base_list_field:          SonataAdminBundle:CRUD:base_list_field.html.twig

    sonata_block:
        default_contexts: [cms]
        blocks:
            sonata.admin.block.admin_list:
                contexts: [admin]
            sonata.block.service.text: ~
            sonata.block.service.rss: ~

    sonata_doctrine_orm_admin:
        entity_manager: ~

    sonata_user:
        class:
            user: Ripple\UserBundle\Entity\User  #change this to your application User entity
            group: Ripple\UserBundle\Entity\Group #change this to your application Group entity
        admin:
            user:
                class: Ripple\AdminBundle\Admin\Entity\UserAdmin #change this to your application UserAdmin service (see "Extending and Overriding the bundle" first)
        security_acl: true
        manager_type: orm

Finally, we need to add all of our new bundles into `app/AppKernel.php`:

    <?php

    use Symfony\Component\HttpKernel\Kernel;
    use Symfony\Component\Config\Loader\LoaderInterface;

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new FOS\UserBundle\FOSUserBundle(),
                new Ripple\UserBundle\RippleUserBundle(),
                new Sonata\BlockBundle\SonataBlockBundle(),
                new Sonata\jQueryBundle\SonatajQueryBundle(),
                new Sonata\AdminBundle\SonataAdminBundle(),
                new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
                new Sonata\UserBundle\SonataUserBundle('VendorUserBundle'), #this should take the name of your application user bundle
                new Knp\Bundle\MenuBundle\KnpMenuBundle(),
                new Ripple\AdminBundle\RippleAdminBundle()
            );
        }
    }


Extending and Overriding the bundle
------------------------------------

We need to create an `AdminBundle` for your application, which will contain all of your application level overrides
for anything admin related. You may notice in the above configuration that there is a reference to a service class called
`Ripple\AdminBundle\Entity\UserAdmin`, which tells `SonataAdminBundle` what fields to display and what they should look
like.

Create a new bundle at `Vendor\AdminBundle`, with a Bundle build file (`Vendor\AdminBundle\VendorAdminBundle`) containing the following:

    <?php

    namespace Vendor\AdminBundle;

    use Symfony\Component\HttpKernel\Bundle\Bundle;

    /**
     * Administration bundle.
     *
     * @package Vendor\AdminBundle
     */
    class VendorAdminBundle extends Bundle
    {
    }

To override fields used in the admin interface for Users, we need to create a new service class (`Vendor\AdminBundle\Admin\Entity\UserAdmin`):

    <?php

    namespace MyFood\AdminBundle\Admin\Entity;

    use Ripple\AdminBundle\Admin\Entity\UserAdmin as RippleUserAdmin;

    /**
     * User admin configuration class.
     *
     * This class extends the Ripple provided user admin configuration class.
     *
     * @package Vendor\AdminBundle\Admin\Entity
     */
    class UserAdmin extends RippleUserAdmin
    {
        /**
         * Configures fields available in the add/edit form.
         *
         * @param FormMapper $formMapper The form mapper
         *
         * @return void
         */
        protected function configureFormFields(FormMapper $formMapper)
        {
            parent::configureFormFields($formMapper);

            // add any custom form fields here
        }

        /**
         * Configures fields that are available in the data grid.
         *
         * @param DatagridMapper $datagridMapper The datagrid mapper
         *
         * @return void
         */
        protected function configureDatagridFilters(DatagridMapper $datagridMapper)
        {
            parent::configureDatagridFilters($datagridMapper);

            // add any custom datagrid fields here
        }

        /**
         * Configures fields that are visible in the list view.
         *
         * @param ListMapper $listMapper The list field mapper
         *
         * @return void
         */
        protected function configureListFields(ListMapper $listMapper)
        {
            parent::configureListFields($listMapper);

            // add any custom list fields here
        }

        /**
         * Configures fields that are visible in the "Show" view
         *
         * @param ShowMapper $showMapper The show field mapper
         *
         * @return void
         */
        public function configureShowFields(ShowMapper $showMapper)
        {
            parent::configreShowFields($showMapper);

            // add any custom "show" fields here
        }
    }

For an idea of how to add fields to views, and what these various views mean check the [SonataAdminBundle documentation][1].

Overriding Template Blocks
---------------------------

The admin bundle is built upon blocks re-usable template blocks which can be overridden in the `app/config/admin.yml`
 file that we created during the installation process. In the `sonata_admin.templates` config section, you will find a
 list of templates:

    # default global templates
    layout:  RippleAdminBundle::standard_layout.html.twig
    ajax:    SonataAdminBundle::ajax_layout.html.twig
    dashboard: SonataAdminBundle:Core:dashboard.html.twig

    # default actions templates, should extend a global templates
    list:    SonataAdminBundle:CRUD:list.html.twig
    show:    SonataAdminBundle:CRUD:show.html.twig
    edit:    SonataAdminBundle:CRUD:edit.html.twig

    user_block:               RippleAdminBundle:Sonata:user_block.html.twig
    preview:                  SonataAdminBundle:CRUD:preview.html.twig
    history:                  SonataAdminBundle:CRUD:history.html.twig
    history_revision:         SonataAdminBundle:CRUD:history_revision.html.twig
    action:                   SonataAdminBundle:CRUD:action.html.twig
    list_block:               SonataAdminBundle:Block:block_admin_list.html.twig
    short_object_description: SonataAdminBundle:Helper:short-object-description.html.twig
    delete:                   SonataAdminBundle:CRUD:delete.html.twig
    batch:                    SonataAdminBundle:CRUD:list__batch.html.twig
    batch_confirmation:       SonataAdminBundle:CRUD:batch_confirmation.html.twig
    inner_list_row:           SonataAdminBundle:CRUD:list_inner_row.html.twig
    base_list_field:          SonataAdminBundle:CRUD:base_list_field.html.twig

These can all be overridden to point at your own bundle's view templates (good practice is to place your templates below
the `Vendor\AdminBundle\Resources\views` section, and make it correspond with the default template location. For example,
if we were to override the `delete` template block, we would place it in `Vendor\AdminBundle\Resources\views\CRUD\delete.html.twig`.

[1]: http://sonata-project.org/bundles/admin/master/doc/index.html