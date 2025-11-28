<?php
trait Users
{
	private function generateRandomPassword($length = 12)
	{
		// Define character pools
		$lowercase = 'abcdefghijklmnopqrstuvwxyz';
		$uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$digits    = '0123456789';
		$special   = '!@#%^&*()-_=+[]{};:,.<>?';

		// Combine all pools
		$allChars = $lowercase . $uppercase . $digits . $special;

		$password = '';

		// Guarantee at least one of each type
		$password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
		$password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
		$password .= $digits[random_int(0, strlen($digits) - 1)];
		$password .= $special[random_int(0, strlen($special) - 1)];

		// Fill the rest randomly
		for ($i = strlen($password); $i < $length; $i++) {
			$password .= $allChars[random_int(0, strlen($allChars) - 1)];
		}

		// Shuffle to avoid predictable placement
		return str_shuffle($password);
	}

	public function SetRandomPasswords()
	{
		$this->InstallerRequired("json");
		$users = usersModel::getAll();
		$passwords = [];
		foreach ($users as $user)
		{
			$password = $this->generateRandomPassword(8);
			$user->setPasswordHash(password_hash($password, PASSWORD_BCRYPT));
			$user->save();
			$passwords[] = [
				"user" => $user->getNickname(),
				"password" => $password
			];
		}
		$this->json($passwords);
	}
}
?>
