<?php

namespace Okta\Resources;

/**
 * Implementation of the Okta Users resource, accessible via $oktaClient->user
 *
 * http://developer.okta.com/docs/api/resources/users.html
 */
class User extends Base
{

    /**
     * Creates a new user in your Okta organization with or without credentials.
     *
     * @param  array   $profile     Array of user profile properties
     * @param  array   $credentials Array of user credentials
     * @param  boolean $activate    Weather or not to execute activation
     *                              lifecycle operation when creating the user
     *
     * @return object               User model object
     */
    public function create($profile, $credentials = [], $activate = true) {

        $request = $this->request->post('users');

        $request->data([
            'profile'     => $profile,
            'credentials' => $credentials,
            'activate'    => $activate
        ]);

        return $request->send();

    }

    /**
     * Fetches a user from your Okta organization.
     *
     * @param  string $uid User ID
     *
     * @return object      User model object
     */
    public function get($uid) {

        $request = $this->request->get('users/' . $uid);

        return $request->send();

    }

    /**
     * Enumerates users in your organization with pagination. A subset of users
     * can be returned that match a supported filter expression or query.
     *
     * @param  string  $q      Searches firstName, lastName, and email
     *                         properties of users for matching value
     * @param  int     $limit  Specified the number of results
     * @param  string  $filter Filter expression for users
     * @param  boolean $after  Specifies the pagination cursor for the next page of users
     *
     * @return array           Array of user objects
     */
    public function listUsers($q = null, $limit = null, $filter = null, $after = null) {

        $request = $this->request->get('users');

        if (isset($q)) {
            $this->query(['q' => $q]);
        }

        if (isset($limit)) {
            $this->query(['limit' => $limit]);
        }

        if (isset($filter)) {
            $this->query(['filter' => $filter]);
        }

        if (isset($after)) {
            $this->query(['after' => $after]);
        }

        return $request->send();

    }

    /**
     * Update a user’s profile and/or credentials with partial update semantics.
     * @param  string $uid         ID of user to update
     * @param  array  $profile     Array of user profile properties
     * @param  array  $credentials Array of credential properties
     *
     * @return object              Updated user object
     */
    public function update($uid, $profile = null, $credentials = null) {

        $request = $this->request->post('users/' . $uid);

        if (isset($profile)) {
            $request->data(['profile' => $profile]);
        }

        if (isset($credentials)) {
            $request->data(['credentials' => $credentials]);
        }

        return $request->send();

    }

    /**
     * Fetches appLinks for all direct or indirect (via group membership)
     * assigned applications.
     *
     * @param  string $uid ID of user
     *
     * @return array       Array of App Links
     */
    public function apps($uid) {

        $request = $this->request->get('users/' . $uid . '/appLinks');

        return $request->send();

    }

    /**
     * Fetches the groups of which the user is a member.
     *
     * @param  string $uid ID of user
     *
     * @return array       Array of group objects
     */
    public function groups($uid) {

        $request = $this->request->get('users/' . $uid . '/groups');

        return $request->send();

    }

    /**
     * Activates a user. This operation can only be performed on users with a
     * STAGED status. Activation of a user is an asynchronous operation. The
     * user will have the transitioningToStatus property with a value of ACTIVE
     * during activation to indicate that the user hasn’t completed the
     * asynchronous operation. The user will have a status of ACTIVE when the
     * activation process is complete.
     *
     * @param  string $uid       ID of user
     * @param  bool   $sendEmail Sends an activation email to the user if true
     *
     * @return object            Returns empty object by default. When sendEmail
     *                           is false, returns an activation link for the
     *                           user to set up their account.
     */
    public function activate($uid, $sendEmail = null) {

        $request = $this->request->post('users/' . $uid . '/lifecycle/activate');

        if (isset($sendEmail)) {
            $request->query(['sendEmail' => $sendEmail]);
        }

        return $request->send();

    }

    /**
     * Deactivates a user. This operation can only be performed on users that do
     * not have a DEPROVISIONED status. Deactivation of a user is an
     * asynchronous operation. The user will have the transitioningToStatus
     * property with a value of DEPROVISIONED during deactivation to indicate
     * that the user hasn’t completed the asynchronous operation. The user will
     * have a status of DEPROVISIONED when the deactivation process is complete.
     *
     * @param  string $uid ID of user
     *
     * @return empty       Returns an empty object
     */
    public function deactivate($uid) {

        $request = $this->request->post('users/' . $uid . '/lifecycle/deactivate');

        return $request->send();

    }

    /**
     * Suspends a user. This operation can only be performed on users with ans
     * ACTIVE status. The user will have a status of SUSPENDED when the process
     * is complete.
     *
     * Passing an invalid id returns a 404 Not Found status code with error code
     * E0000007. Passing an id that is not in the ACTIVE state returns a 400 Bad
     * Request status code with error code E0000001.
     *
     * @param  string $uid ID of user
     *
     * @return object      Returns an empty object
     */
    public function suspend($uid) {

        $request = $this->request->post('users/:id/lifecycle/suspend');

        return $request->send();

    }

    /**
     * Unsuspends users and returns then to the ACTIVE state. This operation can
     * only be performed on users that have a SUSPENDED status.
     *
     * Passing an invalid id returns a 404 Not Found status code with error code
     * E0000007. Passing an id that is not in the SUSPENDED state returns a 400
     * Bad Request status code with error code E0000001.
     *
     * @param  string $uid ID of user
     *
     * @return object      Returns an empty object
     */
    public function unsuspend($uid) {

        $request = $this->request->post('users/' . $uid . '/lifecycle/unsuspend');

        return $request->send();

    }

    /**
     * Unlocks a user with a LOCKED_OUT status and returns them to ACTIVE
     * status. Users will be able to login with their current password.
     *
     * @param  string $uid ID of user
     *
     * @return object      Returns an empty object
     */
    public function unlock($uid) {

        $request = $this->request->post('users/' . $uid . '/lifecycle/unlock');

        return $request->send();

    }

    /**
     * Generates a one-time token (OTT) that can be used to reset a user’s
     * password. The OTT link can be automatically emailed to the user or
     * returned to the API caller and distributed using a custom flow.
     *
     * This operation will transition the user to the status of RECOVERY and the
     * user will not be able to login or initiate a forgot password flow until
     * they complete the reset flow.
     *
     * @param  string $uid       ID of user
     * @param  bool   $sendEmail Sends reset password email to the user if true
     *
     * @return object            Returns an empty object by default. When
     *                           sendEmail is false, returns a link for the user
     *                           to reset their password.
     */
    public function resetPassword($uid, $sendEmail) {

        $request = $this->request->post('users/' . $uid . '/lifecycle/reset_password');

        if (isset($sendEmail)) {
            $request->query(['sendEmail' => $sendEmail]);
        }

        return $request->send();

    }

    /**
     * This operation will transition the user to the status of PASSWORD_EXPIRED
     * and the user will be required to change their password at their next
     * login. If tempPassword is passed, the user’s password is reset to a
     * temporary password that is returned, and then the temporary password is
     * expired.
     *
     * @param  string $uid          User ID
     * @param  string $tempPassword New temporary password
     *
     * @return object               User object
     */
    public function expirePassword($uid, $tempPassword = null) {

        $request = $this->request->post('users/' . $uid . '/lifecycle/expire_password');

        if (isset($tempPassword)) {
            $request->query(['tempPassword' => $tempPassword]);
        }

        return $request->send();

    }

    /**
     * This operation resets all factors for the specified user. All MFA factor
     * enrollments returned to the unenrolled state. The user’s status remains
     * ACTIVE. This link is present only if the user is currently enrolled in
     * one or more MFA factors.
     *
     * @param  string $uid ID of user
     *
     * @return object      Returns an empty object by default
     */
    public function resetFactors($uid) {

        $request = $this->request->post('users/' . $uid . '/lifecycle/reset_factors');

        return $request->send();

    }

    /**
     * Generates a one-time token (OTT) that can be used to reset a user’s
     * password. This operation can only be performed on users with a valid
     * recovery question credential and have an ACTIVE status.
     *
     * @param  string $uid       User ID
     * @param  bool   $sendEmail Sends a forgot password email to the user if true
     *
     * @return object            Returns an empty object by default. Whe
     *                           sendEmail is false, returns a link for the user
     *                           to reset their password.
     */
    public function forgotPassword($uid, $sendEmail = null) {

        $request = $this->request->post('users/' . $uid . '/credentials/forgot_password');

        if (isset($sendEmail)) {
            $request->query(['sendEmail' => $sendEmail]);
        }

        return $request->send();

    }

    /**
     * Changes a user’s password by validating the user’s current password. This
     * operation can only be performed on users in STAGED, ACTIVE,
     * PASSWORD_EXPIRED, or RECOVERY status that have a valid password
     * credential
     *
     * @param  string $uid     User ID
     * @param  string $oldPass Current password for user
     * @param  string $newPass New passwor for user
     *
     * @return object          User credentials object
     */
    public function changePassword($uid, $oldPass, $newPass) { // resetPassword()

        $request = $this->request->post('users/' . $uid . '/credentials/change_password');

        $request->data(['oldPassword' => $oldPass, 'newPassword' => $newPass]);

        return $request->send();

    }

    /**
     * Modify a users's security question
     *
     * @param  string $uid      User ID
     * @param  string $password User's password
     * @param  string $question New securrity question
     * @param  string $answer   Answer to security question
     *
     * @return object           Security question object
     */
    public function changeRecoveryQuestion($uid, $password, $question, $answer) {

        $request = $this->request->post('users/' . $uid . '/credentials/change_recovery_question');

        $request->data([
            'password' => [
                'value' => $password
            ],
            'recovery_question' => [
                'question' => $question,
                'answer'   => $answer
            ]
        ]);

        return $request->send();

    }

}
