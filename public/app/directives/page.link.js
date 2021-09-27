angular.module('siaApp').directive('pageLink', pageLinkFunction);

pageLinkFunction.$inject = ['dataFactory', '$route'];

function pageLinkFunction(dataFactory, route)
{
    return {
        restrict: "C",
        link: link
    }

    function link(scope, element, attrs)
    {
        let routeParams = route.current.params;
        element.on('click', () => {
            dataFactory.current_page = attrs.page;
            dataFactory.getData(routeParams).then((responseData) => {
                scope.vm.data = responseData.data;
                scope.vm.pageData = responseData;
            });
        });
    }
}