(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalMataKuliah', modalMataKuliah);

  modalMataKuliah.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalMataKuliah(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/mata_kuliah',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;
      activate();

      scope.addMatkul = addMatkul;

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function addMatkul() {
        let data = {
          dosen_pjmk_id: dm.data.id,
          mata_kuliah_id: scope.vm.data.mata_kuliah_id
        };

        dataservice.postData('mata_kuliah_diampuh', data).then(function(data){
          $(element).modal('hide');
          state.reload();
        });
      }
    }
  }
})();
