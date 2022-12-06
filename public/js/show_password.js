window.onload = () => {
  const url = window.location.origin;

  const incorrectPassword = document.querySelector("#incorrectPassword");
  const PasswordForm = document.querySelector("#passwordForm");
  const decryptedData = document.querySelector("#decryptedPassword");

  let privateKey;
  let encryptedPassword;

  showPasswordBtns = document.querySelectorAll(".showpassword");
  showPasswordBtns.forEach((btn) => {
    btn.addEventListener("click", function handleClick(event) {
      privateKey = btn.getAttribute("data-privateKey").toString().trim();
      encryptedPassword = btn
        .getAttribute("data-encryptedPassword")
        .toString()
        .trim();
    });
  });

  PasswordForm.addEventListener("submit", onSubmit);

  async function onSubmit(event) {
    const password = PasswordForm.elements["userPassword"].value;
    const passwordINput = PasswordForm.elements["userPassword"];

    event.preventDefault();

    console.log(privateKey);
    console.log(encryptedPassword);

    let result = await axios.post(url + "/users/verify", { password });

    if (result.data.isCorrectPassword) {
      incorrectPassword.innerHTML = "";
      decryptedData.style.display = "block";

      const key = decryptData(password, privateKey);
      const Realpassword = decryptData(key, encryptedPassword);

      console.log("DEcrypted Data ", "uC5!cW5(pY3$zI5<hK1#");
    } else {
      incorrectPassword.innerHTML = "Mot de Pass incorrecty";
    }
  }
};
