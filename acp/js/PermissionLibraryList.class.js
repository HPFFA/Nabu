/**
 * @author	Jana Pape
 * @copyright	2010
 */
function PermissionLibraryList(libraryStructure, globalPermissions, libraryPermissions, type) {
	this.libraryStructure = libraryStructure;
	this.globalPermissions = globalPermissions;
	this.libraryPermissions = libraryPermissions;
	this.activePermission = '';
	this.type = type;
	
	/**
	 * Initialises the library list.
	 */
	this.init = function() {
		// add onchange listener
		var permissionSelect = document.getElementById('permissionName');
		permissionSelect.list = this;
		permissionSelect.onchange = function() { this.list.setActivePermission(this.options[this.selectedIndex].value); };
		
		// add onclick listener
		this.addOnclickListener(0);
	}
	
	/**
	 * Sets the name of the selected permission.
	 */
	this.setActivePermission = function(permissionName) {
		if (permissionName) {
			this.activePermission = permissionName;
			
			// refresh checkbox status
			this.refreshSettings(permissionName, 0);
			
			// refresh status icon
			this.refreshStatus(permissionName, 0, -1);
			
			// make visible
			this.showLibraryList(true);
		}
		else {
			this.activePermission = '';
			this.showLibraryList(false);
		}
	}
	
	/**
	 * Refreshes the checkboxes.
	 */
	this.refreshSettings = function(permissionName, parentID) {
		if (!this.libraryStructure[parentID]) return;
		
		for (var i = 0; i < this.libraryStructure[parentID].length; i++) {
			var libraryID = this.libraryStructure[parentID][i];
			
			// get setting
			var value = this.getLibraryPermission(permissionName, libraryID);
			
			// show setting
			document.getElementById('allow'+libraryID).checked = (value == 1 ? true : false);
			document.getElementById('deny'+libraryID).checked = (value == 0 ? true : false);
			
			// refresh children
			this.refreshSettings(permissionName, libraryID);
		}
	}
	
	/**
	 * Refreshes the status icon.
	 */
	this.refreshStatus = function(permissionName, parentID, parentValue) {
		if (!this.libraryStructure[parentID]) return;
		
		for (var i = 0; i < this.libraryStructure[parentID].length; i++) {
			var libraryID = this.libraryStructure[parentID][i];
			
			// get setting
			var value = this.getLibraryPermission(permissionName, libraryID);
			var inheritValue = value;
			
			// take parent
			if (value == -1) {
				if (parentValue != -1) {
					value = parentValue;
					inheritValue = value;
				}
				else {
					value = this.getGlobalPermission(permissionName, libraryID);
				}
			}
			
			// show status
			document.getElementById('status'+libraryID).src = (value == 1 ? RELATIVE_WCF_DIR + 'icon/enabledS.png' : RELATIVE_WCF_DIR + 'icon/disabledS.png');
			
			// refresh children
			this.refreshStatus(permissionName, libraryID, inheritValue);
		}
	}
	
	/**
	 * Returns the value of a library permission.
	 */
	this.getLibraryPermission = function(permissionName, libraryID) {
		var value = -1;
		if (permissionName == 'fullControl') {
			var globalPermissions = this.getGlobalPermissions(libraryID);
			for (permission in globalPermissions) {
				if (this.libraryPermissions[libraryID] && this.libraryPermissions[libraryID][permission] == 1) var newValue = 1;
				else if (this.libraryPermissions[libraryID] && this.libraryPermissions[libraryID][permission] == 0) var newValue = 0;
				else {
					value = -1;
					break;
				}
				
				if (value == -1) value = newValue;
				else if (value != newValue) {
					value = -1;
					break;
				}
			}
		}
		else {
			if (this.libraryPermissions[libraryID] && this.libraryPermissions[libraryID][permissionName] == 1) value = 1;
			else if (this.libraryPermissions[libraryID] && this.libraryPermissions[libraryID][permissionName] == 0) value = 0;
		}
		
		return value;
	}
	
	/**
	 * Adds the onclick listener to the checkboxes.
	 */
	this.addOnclickListener = function(parentID) {
		if (!this.libraryStructure[parentID]) return;
		
		for (var i = 0; i < this.libraryStructure[parentID].length; i++) {
			var libraryID = this.libraryStructure[parentID][i];
			
			// add listener
			var allow = document.getElementById('allow'+libraryID);
			allow.list = this;
			allow.libraryID = libraryID;
			allow.onclick = function() { this.list.allow(this.libraryID, this.checked); };
			
			var deny = document.getElementById('deny'+libraryID);
			deny.list = this;
			deny.libraryID = libraryID;
			deny.onclick = function() { this.list.deny(this.libraryID, this.checked); };
			
			// refresh children
			this.addOnclickListener(libraryID);
		}
	}
	
	/**
	 * Receives a click on a allow checkbox.
	 */
	this.allow = function(libraryID, checked) {
		if (!this.libraryPermissions[libraryID]) this.libraryPermissions[libraryID] = new Object();
		if (this.activePermission == 'fullControl') {
			this.allowFullControl(libraryID, checked);
		}
		else {
			this.libraryPermissions[libraryID][this.activePermission] = (checked ? 1 : -1);
		}
		this.refresh();
	}
	
	/**
	 * Allows all permissions for a library.
	 */
	this.allowFullControl = function(libraryID, checked) {
		var globalPermissions = this.getGlobalPermissions(libraryID);
		for (permission in globalPermissions) {
			this.libraryPermissions[libraryID][permission] = (checked ? 1 : -1);
		}
	}
	
	/**
	 * Denies all permissions for a library.
	 */
	this.denyFullControl = function(libraryID, checked) {
		var globalPermissions = this.getGlobalPermissions(libraryID);
		for (permission in globalPermissions) {
			this.libraryPermissions[libraryID][permission] = (checked ? 0 : -1);
		}
	}
	
	/**
	 * Receives a click on a deny checkbox.
	 */
	this.deny = function(libraryID, checked) {
		if (!this.libraryPermissions[libraryID]) this.libraryPermissions[libraryID] = new Object();
		if (this.activePermission == 'fullControl') {
			this.denyFullControl(libraryID, checked);
		}
		else {
			this.libraryPermissions[libraryID][this.activePermission] = (checked ? 0 : -1);
		}
		this.refresh();
	}
	
	/**
	 * Refreshes the complete list.
	 */
	this.refresh = function() {
		this.setActivePermission(this.activePermission);
	}
	
	/**
	 * Makes the library list visible.
	 */
	this.showLibraryList = function(show) {
		document.getElementById('libraryList').style.display = (show ? '' : 'none');
	}
	
	/**
	 * Saves the selected permissions in hidden input fields.
	 */
	this.submit = function(form) {
		for (var libraryID in this.libraryPermissions) {
			for (var permissionName in this.libraryPermissions[libraryID]) {
				var typeField = document.createElement('input');
				typeField.type = 'hidden';
				typeField.name = 'libraryPermissions[' + libraryID + '][' + permissionName + ']';
				typeField.value = this.libraryPermissions[libraryID][permissionName];
				form.appendChild(typeField);
			}
		}
	}
	
	/**
	 * Returns the list of global permissions.
	 */
	this.getGlobalPermissions = function(libraryID) {
		if (type == 'group') return this.globalPermissions;
		else return this.globalPermissions[libraryID];
	}
	
	/**
	 * Returns the value of a global permission.
	 */
	this.getGlobalPermission = function(permissionName, libraryID) {
		var globalPermissions = this.getGlobalPermissions(libraryID);
		var value = -1;
		
		if (permissionName == 'fullControl') {
			for (permission in globalPermissions) {
				if (globalPermissions[permission] == 1) var newValue = 1;
				else if (globalPermissions[permission] == 0) var newValue = 0;
				else {
					value = -1;
					break;
				}
				
				if (value == -1) value = newValue;
				else if (value != newValue) {
					value = -1;
					break;
				}
			}
		}
		else {
			value = globalPermissions[permissionName];
		}
		
		return value;
	}
	
	this.init();
}
