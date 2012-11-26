/*
 * Copyright (c) 2012, Bret Pimentel. 
 * This program is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License 
 * as published by the Free Software Foundation; either version 2 
 * of the License, or (at your option) any later version. 
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details. 
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the Free Software 
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. 
*/

(function ($) {

	'use strict';

	var options = false,
		target = 'body';

	if (window.accidentals) {
		options = window.accidentals;
		if (options.hasOwnProperty('target')) {
			target = options.target;
		}
		if (options.hasOwnProperty('safeMode')) {
			options.safeMode = parseInt(options.safeMode, 10); //"false" gets passed in by wp_localize_script as a string of value "0", which Javascript interprets as true
		}
	}

	$(target).accidentals(options);

}(jQuery));