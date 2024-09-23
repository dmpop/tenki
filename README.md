# Tenki

Tenki a simple PHP application for logging current weather conditions, notes, and waypoints.

The accompanying shell script can be used to read the date of a specified photo then find a matching text file, and write its contents into the _Comment_ field of the photo.

The [Linux Photography](https://gumroad.com/l/linux-photography) book provides detailed information on using Tenki. Get your copy at [Google Play Store](https://play.google.com/store/books/details/Dmitri_Popov_Linux_Photography?id=cO70CwAAQBAJ) or [Gumroad](https://gumroad.com/l/linux-photography).

<img src="https://cameracode.coffee/uploads/linux-photography.png" title="Linux Photography" width="300"/>

## Run Tenki in a container

Perform the following steps on the machine you want to use to serve Tenki.

1. Install [Docker](https://www.docker.com/).
2. Create a directory for storing Tenki data.
3. Clone the Tenki Git repository using the `git clone https://github.com/dmpop/tenki.git` command.
4. Switch to the _tenki_ directory.
5. Open the _config.php_ file for editing, and modify the available settings. Save the changes.
6. Build an image using the `docker build -t tenki .` command.
7. Run a container: `docker run -d --rm -p 8080:8080 --name=tenki -v /path/to/data:/usr/src/tenki/data:rw tenki` (replace _/path/to/data_ with the actual path to the created directory).
8. Point the browser to _http://127.0.0.1:8080_ (replace _127.0.0.1_ with the actual IP address or domain name of the machine running the container).

## Author

Dmitri Popov [dmpop@cameracode.coffee](mailto:dmpop@cameracode.coffee)

## License

The [GNU General Public License version 3](http://www.gnu.org/licenses/gpl-3.0.en.html)
