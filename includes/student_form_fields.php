<?php /* Shared fields. Expects $data (array); optional $lockStudentNo, $passwordRequired. */ ?>
<div class="grid">
  <label>Full name
    <input type="text" name="name" value="<?= e($data['name']) ?>" required>
  </label>
  <label>Email
    <input type="email" name="email" value="<?= e($data['email']) ?>" required>
  </label>
  <label>Student number
    <input type="text" name="student_no" value="<?= e($data['student_no']) ?>" <?= !empty($lockStudentNo) ? 'readonly' : 'required' ?>>
  </label>
  <label>Course
    <input type="text" name="course" value="<?= e($data['course']) ?>" placeholder="e.g. BS Information Technology">
  </label>
  <label>Year level
    <select name="year_level">
      <?php for ($i = 1; $i <= 6; $i++): ?>
        <option value="<?= $i ?>" <?= (int)$data['year_level'] === $i ? 'selected' : '' ?>>Year <?= $i ?></option>
      <?php endfor; ?>
    </select>
  </label>
  <label>Phone
    <input type="text" name="phone" value="<?= e($data['phone']) ?>">
  </label>
  <label class="full">Address
    <input type="text" name="address" value="<?= e($data['address']) ?>">
  </label>
  <label class="full"><?= !empty($passwordRequired) ? 'Password' : 'New password (leave blank to keep the current one)' ?>
    <input type="password" name="password" autocomplete="new-password" minlength="6" <?= !empty($passwordRequired) ? 'required' : '' ?>>
  </label>
</div>
