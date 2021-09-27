angular.module('siaApp').component('navBar', {
    controller: navBarController,
    controllerAs: 'vm',
    templateUrl: '/menu/view'
});

navBarController.$inject = ['dataFactory', '$route', '$scope'];

function navBarController(dataFactory, route, scope)
{
    let vm = this;
    vm.menus = {};
    vm.active_parent = false;
    vm.active_model = route.current.params.model;

    vm.checkMenu = checkMenu;

    scope.$watch(() => {
        return dataFactory.menus;
    }, (newValue) => {
        vm.menus = newValue;
        activate();
    });

    scope.$watch(() => {
        return dataFactory.active_model;
    }, (newValue) => {
        if (newValue) {
            vm.active_model = newValue;
            vm.active_parent = dataFactory.active_parent;
            updateCurrentMenu();
        }
    });

    function activate()
    {
        updateCurrentMenu();
    }

    function updateCurrentMenu()
    {
        let routeParams = route.current.params;
        dataFactory.active_menu = findAllByKey(dataFactory.menus, routeParams.model);
    }

    function findAllByKey(obj, keyToFind) 
    {
        return Object.entries(obj)
            .reduce((acc, [key, value]) => {
                if(key == keyToFind)
                {
                    value['model'] = key;
                    return Object.assign(acc, value);
                }else
                {
                    if(typeof value === 'object')
                    {
                        return Object.assign(acc, findAllByKey(value, keyToFind))
                    }else{
                        return acc;
                    }
                }
            }, {});
    }

    function checkMenu(keyMenu, keyParent)
    {
        if (keyMenu == vm.active_model) {
            vm.active_parent = keyParent;
            return true;
        }
        return false;
    }

    
}