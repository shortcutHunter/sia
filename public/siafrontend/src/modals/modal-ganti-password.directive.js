(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalGantiPassword', modalGantiPassword);

  modalGantiPassword.$inject = ['$state', 'dataservice', 'logger'];

  /* @ngInject */
  function modalGantiPassword(state, dataservice, logger) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/ganti_password',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;

      scope.gantiPassword = gantiPassword;

      activate();

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function gantiPassword() {

        if (!scope.vm || !scope.vm.data || !scope.vm.data.password) {
          logger.error("Password tidak dapat kosong");
          return;
        }

        scope.data.pass = scope.vm.data.password;

        return dataservice.postData('user', scope.data, scope.data.id).then(function(response){
          $(element).modal('hide');
          state.reload();
        });
      }

    }
  }
})();
