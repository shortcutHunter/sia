(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalTolakPengajuan', modalTolakPengajuan);

  modalTolakPengajuan.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalTolakPengajuan(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/tolak_pengajuan',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;

      scope.tolak = tolak;

      activate();

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function tolak() {
        let data = {status: 'tolak', alasan: scope.vm.data.alasan};
        dataservice.postData('pengajuan_ks', data, scope.$parent.pengajuan_ks.id).then(function(response){
          $(element).modal('hide');
          scope.$parent.closeModal();
          state.reload();
        });
      }
    }
  }
})();
