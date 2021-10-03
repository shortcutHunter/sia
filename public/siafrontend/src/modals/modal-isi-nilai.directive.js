(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalIsiNilai', modalIsiNilai);

  modalIsiNilai.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalIsiNilai(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/isi_nilai',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;
      scope.form_data = {};
      scope.isiNilai = isiNilai;

      activate();

      function activate() {
        $(element).modal('show');

        getMahasiswa();

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function getMahasiswa() {
        dataservice.getUrl(`/matkul_diampuh/${scope.data.id}/get/mahasiswa`).then(function(response){
          scope.data_mahasiswa = response.data;

          $.each(scope.data_mahasiswa, function(i, v){
            if (v.nilai) {
              $.each(v.nilai.riwayat_belajar_nilai, function(){
                scope.form_data[v.id + '_' + this.nilai_id] = this.nilai;
              });
            }
          });

        });
      }

      function isiNilai(action) {
        let mahasiswa_data = {};
        $.each(scope.form_data, function(i, v){
          let raw_data = i.split('_');
          let mahasiswa_id = raw_data[0];
          let nilai_id = raw_data[1];
          if (!mahasiswa_data[mahasiswa_id]) {
            mahasiswa_data[mahasiswa_id] = [];
          }
          mahasiswa_data[mahasiswa_id].push({
            nilai_id: nilai_id,
            nilai: v
          });
        });

        let data = [];
        $.each(mahasiswa_data, function(i, v){
          let riwayat_belajar = {
            mahasiswa_id: i,
            semester_id: scope.$parent.vm.data.semester_id,
            riwayat_belajar_detail: {
              mata_kuliah_id: scope.data.mata_kuliah_id,
              riwayat_belajar_nilai: v
            }
          };
          data.push(riwayat_belajar);
        });

        dataservice.postDataUrl('/proses/nilai/mahasiswa', {'data': data}).then(function(response){
          $(element).modal('hide');
          state.reload();
        });
      }
    }
  }
})();
