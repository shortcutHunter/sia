angular.module('siaApp').factory('dataFactory', dataFactoryFunction);
dataFactoryFunction.$inject = ['appService'];

function dataFactoryFunction(appService)
{
    const fm = this;
    fm.user = {};
    fm.menus = {};
    fm.current_page = 1;
    fm.active_menu = {};

    fm.getSession = getSession;
    fm.getData = getData;
    fm.getMenu = getMenu;
    fm.getOption = getOption;

    activate();

    return fm;

    function activate()
    {
        fm.getMenu();
        fm.getSession();
    }

    function getSession()
    {
        return appService.httpCall('/session').then((response) => {
            fm.user = response;
            return response;
        });
    }

    function getData(routeParams)
    {
        const model = routeParams.model;
        var url = `/${model}/get`;

        if(routeParams.action == 'tambah')
        {
            return {};
        }
        
        if(routeParams.modelId)
        {
            url = `${url}/${routeParams.modelId}`;
        }

        if (!routeParams.action) {
            url = `${url}?page=${fm.current_page}`;
        }
        
        return appService.httpCall(url).then((response) => {
            response.data.model = model;
            return response.data;
        });
    }

    function getMenu()
    {
        return appService.httpCall('/menu').then((response) => {
            fm.menus = response.data;
            return response.data;
        });
    }

    function getOption(routeParams)
    {
        return appService.httpCall(`/${routeParams.model}/selection`).then((response) => {
            return response.data;
        });
    }
}