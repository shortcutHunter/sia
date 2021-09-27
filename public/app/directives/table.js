angular.module('siaApp').directive('tableRow', tableRowFunction);

tableRowFunction.$inject = ['$location'];

function tableRowFunction(location)
{
    return {
        restrict: 'A',
        link: link
    }

    function link(scope, element, attrs)
    {
        element.on('click', () => {
            const url = `/${attrs.model}/detail/${attrs.tableRow}`;
            location.path(url);
            scope.$apply();
        });
    }
}