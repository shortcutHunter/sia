angular.module('siaApp').directive('btnRedirect', btnRedirectFunction);
btnRedirectFunction.$inject = ['$location']

function btnRedirectFunction(location)
{
    return {
        restrict: 'C',
        link: link
    }

    function link(scope, element, attrs)
    {
        element.on('click', () => {
            location.path(attrs.actionUrl);
            scope.$apply();
        });
    }
}