const dataViewController = function(pageData, dataFactory, scope, option)
{
    let vm = this;
    vm.data = pageData.data;
    vm.model = pageData.model;
    vm.pageData = pageData;
    vm.pageTitle = null;

    vm.option = option;

    scope.$watch(() => dataFactory.active_menu, (newVal) => {
        if (Object.keys(newVal).length > 0) {
            vm.pageTitle = newVal.name;
        }
    });

};

const userFunction = function(dataFactory)
{
    return dataFactory.getSession();
};

const dataFunction = function(dataFactory, route)
{
    const routeParams = route.current.params;
    return dataFactory.getData(routeParams);
};


exports.dataViewController = dataViewController;
exports.userFunction = userFunction;
exports.dataFunction = dataFunction;