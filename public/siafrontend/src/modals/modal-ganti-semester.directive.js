(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalGantiSemester', modalGantiSemester);

  modalGantiSemester.$inject = ['$state', 'dataservice', 'logger'];

  /* @ngInject */
  function modalGantiSemester(state, dataservice, logger) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/ganti_semester',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;

      scope.data_mahasiswa = [];
      scope.form = { semester_selected: false };
      scope.vm = { 
        option: {
          semester: []
        } 
      };

      scope.deleteMahasiswa = deleteMahasiswa;
      scope.gantiSemester = gantiSemester;

      activate();

      scope.$watch('form.semester_selected', (newVal, oldVal) => {
        if (newVal) {
          getMahasiswa(newVal);
        } else {
          scope.data_mahasiswa = [];
        }
      });

      function activate() {
        $(element).modal('show');

        getOption();

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function getOption() {
        dataservice.getUrl(`/semester/get`).then(function(response){
          scope.vm.option.semester = response.data;
        });
      }

      function getMahasiswa(semester) {
        dataservice.getUrl(`/mahasiswa/get?tahun_ajaran_id=${scope.data.id}&semester_id=${semester}`).then(function(response){
          scope.data_mahasiswa = response.data;
        });
      }

      function deleteMahasiswa(idx) {
        scope.data_mahasiswa.splice(idx, 1);
      }

      function gantiSemester() {
        let url = "/tahun_ajaran/ganti/semester";
        let data = {
          mahasiswa: scope.data_mahasiswa.map((v) => v.id),
          semester: scope.form.semester_selected,
          semester_baru: scope.form.semester_baru,
          tahun_ajaran_id: scope.data.id
        };

        return dataservice.postDataUrl(url, data).then(function(response){
          $(element).modal('hide');
          state.reload();
        });
      }

    }
  }
})();
