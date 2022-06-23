(function() {
  'use strict';

  angular
    .module('app.dosen_pjmk')
    .controller('DosenPjmkController', DosenPjmkController);

  DosenPjmkController.$inject = ['$q', 'dataservice', 'logger', '$scope', '$compile'];
  /* @ngInject */
  function DosenPjmkController($q, dataservice, logger, scope, compile) {
    var vm = this;
    vm.title = 'Dosen PJMK';
    vm.table = 'dosen_pjmk';
    vm.page = 0;
    vm.data = [];
    vm.pageData = {};
    vm.searchValue = false;
    vm.filterValue = [
      {
        'field': 'status',
        'value': 'aktif'
      }
    ];
    vm.status_info = "Aktif";
    vm.status = true;
    vm.tahun_ajaran = null;
    vm.semester = null;

    vm.search = search;
    vm.statusChanges = statusChanges;
    vm.cetakdosenPJMK = cetakdosenPJMK;

    activate();

    scope.$watch('vm.tahun_ajaran', tahun_ajaran_changes);
    scope.$watch('vm.semester', semester_changes);

    scope.$watch('vm.page', function(newVal){
      if (newVal != 0) {
        getData();
      }
    });

    scope.$watch('vm.filterValue', function(newVal){
      if (vm.filterValue != null) {
        getData();
      }
    }, true);

    function activate() {
      vm.page = 1;
      getOption();
    }

    function getData() {
      return dataservice.getData(vm.table, vm.page, vm.searchValue, vm.filterValue).then(function(response) {
        vm.pageData = response;
        vm.data = response.data;
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }

    function statusChanges() {
      if (vm.status) {
        vm.status_info = "Aktif";
      }else{
        vm.status_info = "Nonaktif";
      }

      insertFilter('status', vm.status_info.toLowerCase());
    }

    function search() {
      vm.searchValue = {
        'field': 'karyawan',
        'value': angular.copy(vm.searchData)
      };
      if (vm.page == 1) {
        getData();
      }else{
        vm.page = 1;
      }
    }

    function tahun_ajaran_changes(newVal) {
      if (newVal != null) {
        insertFilter('tahun_ajaran_id', newVal);
      }
    }

    function semester_changes(newVal) {
      if (newVal != null) {
        insertFilter('semester_id', newVal);
      }
    }

    function insertFilter(field, val) {

      let status_value = {
        'field': field,
        'value': val
      };

      if (vm.filterValue) {
        let field_name = vm.filterValue.map(function(v, i){ return v.field });
        let array_index = field_name.indexOf(field);

        if (array_index != -1) {
          vm.filterValue[array_index]['value'] = val;
        }else{
          vm.filterValue.push(status_value);
        }
      }else{
        vm.filterValue = [status_value];
      }
    }

    function cetakdosenPJMK() {
      let url = `dosen/pjmk`;
      dataservice.getReport(url).then(function(response) {
        let base64 = response.content;
        scope.fileName = 'List Dosen PJMK.pdf';
        scope.type = 'pdf';
        scope.filetype = 'application/pdf';
        scope.base64 = base64;

        let preview_modal = '<modal-preview file="file" name="fileName" mimetype="filetype" base64="base64" type="type"></modal-preview>';
        let el = compile(preview_modal)(scope);
      });
    }

    scope.$watch('vm.searchData', function(newVal, oldVal) {
      if (newVal != undefined) {
        search();
      }
    });

  }
})();
