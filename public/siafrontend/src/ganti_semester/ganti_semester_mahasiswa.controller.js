(function() {
  'use strict';

  angular
    .module('app.ganti_semester')
    .controller('GantiSemesterMahasiswaController', GantiSemesterMahasiswaController);

  GantiSemesterMahasiswaController.$inject = ['$q', 'dataservice', 'logger', '$scope', '$stateParams', '$compile'];
  /* @ngInject */
  function GantiSemesterMahasiswaController($q, dataservice, logger, scope, stateParams, compile) {
    var vm = this;
    vm.title = 'Ganti Semester';
    vm.data = {};
    vm.option = {};
    vm.form = {};

    vm.lihatDetailTagihan = lihatDetailTagihan;
    vm.checkAll = checkAll;
    // vm.gantiSemester = gantiSemester;

    activate();

    function activate() {
      let promises = [getData(), getOption()];
      return $q.all(promises).then(function() {
        logger.info('Data loaded');
      });
    }

    function getData() {
      return dataservice.getUrl(`/rekap/semester/${stateParams.semester_id}`).then(function(response) {
        vm.data = response.data;
      });
    }

    function getOption() {
      return dataservice.getUrl(`/semester/get`).then(function(response) {
        vm.option.semester = response.data;
        vm.form.semester_id = parseInt(stateParams.semester_id) + 1;
      });
    }

    function lihatDetailTagihan() {
      let mahasiwa = [];
      $.each(vm.form.selected, function(i, v){
        if (v) {mahasiwa.push(i);}
      });

      let data = {
        mahasiswa: mahasiwa,
        semester_id: stateParams.semester_id,
        semester_ganti_id: vm.form.semester_id
      };
      vm.active_data = data;
      let el = "<modal-tagihan-semester data='vm.active_data'></modal-tagihan-semester>";
      el = compile(el)(scope);
    }

    function checkAll() {
      if ($('[name=checkall]').prop('checked')) {
        $('.mahasiswa:not(:checked)').click();
      }else{
        $('.mahasiswa:checked').click();
      }
    }

    // function gantiSemester() {
    //   let mahasiwa = [];
    //   $.each(vm.form.selected, function(i, v){
    //     if (v) {
    //       mahasiwa.push(i);
    //     }
    //   });

    //   let data = {
    //     mahasiwa: mahasiwa,
    //     semester_id: stateParams.semester_id
    //   };
    //   let url = `/rekap/semester/mahasiswa`;
    //   return dataservice.postDataUrl(url, data).then(function(response){
    //     state.reload();
    //   });
    // }
  }
})();
