<?php
/**
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2008-2015 AddThis, LLC                                     |
 * +--------------------------------------------------------------------------+
 * | This program is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by     |
 * | the Free Software Foundation; either version 2 of the License, or        |
 * | (at your option) any later version.                                      |
 * |                                                                          |
 * | This program is distributed in the hope that it will be useful,          |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
 * | GNU General Public License for more details.                             |
 * |                                                                          |
 * | You should have received a copy of the GNU General Public License        |
 * | along with this program; if not, write to the Free Software              |
 * | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA |
 * +--------------------------------------------------------------------------+
 */

if (!interface_exists('AddThisCmsConnectorInterface')) {
	interface AddThisCmsConnectorInterface
	{
	    static function getCmsVersion();
	    static function getCmsMinorVersion();
	    public function getSharingButtonLocations();
	    public function getConfigs();
	    public function getContentTypes();
	    public function saveConfigs($configs = null);
	    public function getSettingsPageUrl();
	    public function getPluginCssFolderUrl();
	    public function getPluginImageFolderUrl();
	    public function getPluginJsFolderUrl();
	}
}