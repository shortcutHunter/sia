(function() {
  'use strict';

  angular
    .module('app.dosen_pa')
    .controller('DosenPaController', DosenPaController);

  DosenPaController.$inject = ['$q', 'dataservice', 'logger', '$scope'];
  /* @ngInject */
  function DosenPaController($q, dataservice, logger, scope) {
    var vm = this;
    vm.title = 'Dosen PA';
    vm.table = 'dosen_pa';
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

    scope.$watch('vm.searchData', function(newVal, oldVal) {
      if (newVal != undefined) {
        search();
      }
    });

  }
})();
