
angular.module('siaApp').directive('customModalDialog', modalDialogFunction);

function modalDialogFunction()
{
    return {
        restrict: 'E',
        link: link,
        templateUrl: function(elem,attrs){
            return attrs.target;
        },
        replace: true,
        scope: true
    }

    function link(scope, element, attrs)
    {        
        scope.vm = {};
        scope.vm.uid = attrs.uid;

        active();
        function active()
        {
            $(element).modal('show');

            $(element).on('shown.bs.modal', () => {
                
            });

            $(element).on('hidden.bs.modal', () => {
                element.remove();
                // scope.destroy();
            });
        }
    }
}