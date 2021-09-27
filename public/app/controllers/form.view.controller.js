const formViewController = function(pageData, routeParams, dataFactory, scope, option)
{
    let vm = this;
    vm.data = pageData.data;
    vm.model = routeParams.model;
    vm.pageTitle = null;
    vm.option = option;

    scope.$watch(() => dataFactory.active_menu, (newVal) => {
        if (Object.keys(newVal).length > 0) {
            vm.pageTitle = newVal.name;
        }
    });
}

const optionFunction = function(dataFactory, route)
{
    const routeParams = route.current.params;
    return dataFactory.getOption(routeParams);
}

exports.formViewController = formViewController;
exports.optionFunction = optionFunction;