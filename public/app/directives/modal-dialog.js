
angular.module('siaApp').directive('modalDialog', modalDialogFunction);

function modalDialogFunction()
{
    return {
        restrict: 'E',
        link: link,
        templateUrl: '/template/component/modal',
        replace: true
    }

    function link(scope, element, attrs)
    {
        scope.vm = scope[attrs.uid];
        scope.vm.uid = attrs.uid;
        
        active();

        function active()
        {
            $(element).modal('show');

            $(element).on('shown.bs.modal', () => {
                if (scope.vm.modalContentElement) {
                    $(element).find('.modal-body').append(scope.vm.modalContentElement);
                }

                if (scope.vm.modalFooterElement) {
                    $(element).find('.modal-footer').append(scope.vm.modalFooterElement);
                }
            });

            $(element).on('hidden.bs.modal', () => {
                element.remove();
                // scope.destroy();
            });
        }
    }
}