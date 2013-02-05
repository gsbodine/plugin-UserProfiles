<?php
class UserProfiles_ProfilesController extends Omeka_Controller_AbstractActionController
{

    public function init()
    {
        $this->_helper->db->setDefaultModelName('UserProfilesProfile');
        $this->_browseRecordsPerPage = get_option('per_page_admin');
    }

    public function editAction()
    {
        $typeId = $this->getParam('type');
        debug($typeId);
        $allTypes = $this->_helper->db->getTable('UserProfilesType')->findAll();
        $profileType = $this->_helper->db->getTable('UserProfilesType')->find($typeId);
        $userId = $this->_getParam('id');
        if($userId) {
            $user = $this->_helper->db->getTable('User')->find($userId);
        } else {
            $user = current_user();
            $userId = $user->id;            
        }      
        $this->view->user = $user; 
        $userProfile = $this->_helper->db->getTable()->findByUserIdAndTypeId($userId, $typeId);
        if(!$userProfile) {
            $userProfile = new UserProfilesProfile();
            $userProfile->setOwner($user);
            $userProfile->type_id = $typeId;
            $userProfile->setRelationData(array('subject_id'=>$userId));
            
        }
        //$userProfile = $this->_helper->db->getTable('UserProfilesProfile')->find(22);
 
        if($this->_getParam('submit') ) {
            $userProfile->setPostData($_POST);
            $userProfile->save();
            fire_plugin_hook('user_profiles_save', array('post'=>$_POST, 'profile'=>$userProfile, 'type'=>$profileType));
            //$this->redirect('user-profiles/profiles/user/id/'. $userId);
        }
        $this->view->profile = $userProfile;
        $this->view->profile_type = $profileType;
        $this->view->profile_types = $allTypes;
    }

    public function deleteAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_forward('method-not-allowed', 'error', 'default');
            return;
        }

        $record = $this->findById();

        $form = $this->_getDeleteForm();

        if ($form->isValid($_POST)) {
            $record->delete();
        } else {
            $this->_forward('error');
            return;
        }

        $successMessage = $this->_getDeleteSuccessMessage($record);
        if ($successMessage != '') {
            $this->flashSuccess($successMessage);
        }
        $user = current_user();
        $this->redirect('user-profiles/profiles/edit/id/' . $user->id);
        //$this->redirect->goto('edit/id/' . $user->id );
    }

    public function userAction()
    {

        $profileTypes = $this->_helper->db->getTable('UserProfilesType')->findAll();
        $userId = $this->_getParam('id');
        if(!$userId) {
            $user = current_user();
            $userId = $user->id;
        }
        $userProfiles = $this->_helper->db->getTable()->findByUserId($userId, true);
        $this->view->user = $this->_helper->db->getTable('User')->find($userId);
        if(empty($this->view->user)) {
            $this->flash("That doesn't seem to be a valid user or user id.");
        }
        $this->view->profiles = $userProfiles;
        $this->view->profile_types = $profileTypes;        
        
    }
}