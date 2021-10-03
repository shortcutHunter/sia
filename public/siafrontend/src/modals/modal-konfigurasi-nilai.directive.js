(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalKonfigurasiNilai', modalKonfigurasiNilai);

  modalKonfigurasiNilai.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalKonfigurasiNilai(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/konfigurasi_nilai',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;
      scope.option_nilai = [];
      scope.konfigurasi_nilai = {};
      scope.konfigurasiNilai = konfigurasiNilai;

      activate();


      function activate() {
        let promise = [getOption()];
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });

        $.each(scope.data.konfigurasi_nilai, function(i, v){
          scope.konfigurasi_nilai[v.nilai_id] = v.persentase;
        });

      }

      function getOption() {
        dataservice.getData('nilai').then(function(response){
          scope.option_nilai = response.data;
        });
      }

      function konfigurasiNilai(action) {
        let data = {
          konfigurasi_nilai: []
        };
        $.each(scope.konfigurasi_nilai, function(i, v){
          data.konfigurasi_nilai.push({
            nilai_id: i,
            persentase: v,
            mata_kuliah_diampuh_id: scope.data.id
          });
        });

        if (action == 'submit') {
          data['terkonfigurasi'] = true;
        }

        dataservice.postData('mata_kuliah_diampuh', data, scope.data.id).then(function(){
          $(element).modal('hide');
          state.reload();
        });
      }
    }
  }
})();
