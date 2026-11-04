package id.keetech.keelaundry;

import android.Manifest;
import android.content.pm.PackageManager;
import android.os.Bundle;
import android.webkit.WebView;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;

import com.dantsu.escposprinter.EscPosPrinter;
import com.dantsu.escposprinter.connection.DeviceConnection;
import com.dantsu.escposprinter.connection.bluetooth.BluetoothConnection;
import com.dantsu.escposprinter.connection.bluetooth.BluetoothPrintersConnections;
import com.dantsu.escposprinter.connection.usb.UsbConnection;
import com.dantsu.escposprinter.connection.usb.UsbPrintersConnections;
import com.getcapacitor.BridgeActivity;

import org.json.JSONArray;
import org.json.JSONObject;

public class MainActivity extends BridgeActivity {

    private static final int PERMISSION_ALL = 1;
    private DeviceConnection selectedPrinter = null;
    private int printerDpi = 203;
    private float printerWidthMm = 48f; // 48mm area cetak efektif untuk kertas 58mm
    private int printerNbrCharactersPerLine = 32;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // Inject Native AndroidInterface ke dalam WebView Capacitor
        WebView webView = this.bridge.getWebView();
        webView.addJavascriptInterface(new WebAppInterface(), "AndroidInterface");
    }

    public class WebAppInterface {

        @android.webkit.JavascriptInterface
        public String scanPrinters() {
            JSONArray printerList = new JSONArray();
            try {
                // 1. Scan Bluetooth Printers (yang sudah di-pair di setting HP)
                if (checkBluetoothPermissions()) {
                    BluetoothConnection[] bluetoothDevices = (new BluetoothPrintersConnections()).getList();
                    if (bluetoothDevices != null) {
                        for (BluetoothConnection device : bluetoothDevices) {
                            JSONObject obj = new JSONObject();
                            obj.put("name", device.getDevice().getName());
                            obj.put("address", device.getDevice().getAddress());
                            obj.put("type", "bluetooth");
                            printerList.put(obj);
                        }
                    }
                }

                // 2. Scan USB Printers (OTG)
                UsbConnection[] usbDevices = (new UsbPrintersConnections(MainActivity.this)).getList();
                if (usbDevices != null) {
                    for (UsbConnection device : usbDevices) {
                        JSONObject obj = new JSONObject();
                        obj.put("name", "USB Printer POS");
                        obj.put("address", "usb");
                        obj.put("type", "usb");
                        printerList.put(obj);
                    }
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
            return printerList.toString();
        }

        @android.webkit.JavascriptInterface
        public void setPrinterSettings(int dpi, float widthMm, int charPerLine) {
            printerDpi = dpi;
            printerWidthMm = widthMm;
            printerNbrCharactersPerLine = charPerLine;
        }

        @android.webkit.JavascriptInterface
        public String connectBluetoothPrinter(String address) {
            try {
                if (!checkBluetoothPermissions()) {
                    return "ERROR: Izin Bluetooth belum diberikan";
                }
                BluetoothConnection[] bluetoothDevices = (new BluetoothPrintersConnections()).getList();
                if (bluetoothDevices != null) {
                    for (BluetoothConnection device : bluetoothDevices) {
                        if (device.getDevice().getAddress().equals(address)) {
                            selectedPrinter = device;
                            return "SUCCESS";
                        }
                    }
                }
                return "ERROR: Perangkat printer tidak ditemukan";
            } catch (Exception e) {
                return "ERROR: " + e.getMessage();
            }
        }

        @android.webkit.JavascriptInterface
        public String connectUsbPrinter() {
            try {
                UsbConnection[] usbDevices = (new UsbPrintersConnections(MainActivity.this)).getList();
                if (usbDevices != null && usbDevices.length > 0) {
                    selectedPrinter = usbDevices[0];
                    return "SUCCESS";
                }
                return "ERROR: Tidak ada printer USB terhubung";
            } catch (Exception e) {
                return "ERROR: " + e.getMessage();
            }
        }

        @android.webkit.JavascriptInterface
        public String printFormattedText(String text) {
            if (selectedPrinter == null) {
                return "ERROR: Printer belum terhubung";
            }
            try {
                EscPosPrinter printer = new EscPosPrinter(selectedPrinter, printerDpi, printerWidthMm,
                        printerNbrCharactersPerLine);
                printer.printFormattedTextAndCut(text);
                return "SUCCESS";
            } catch (Exception e) {
                e.printStackTrace();
                return "ERROR: " + e.getMessage();
            }
        }

        @android.webkit.JavascriptInterface
        public String disconnectPrinter() {
            selectedPrinter = null;
            return "SUCCESS";
        }

        private boolean checkBluetoothPermissions() {
            if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.S) {
                if (ContextCompat.checkSelfPermission(MainActivity.this,
                        Manifest.permission.BLUETOOTH_CONNECT) != PackageManager.PERMISSION_GRANTED ||
                        ContextCompat.checkSelfPermission(MainActivity.this,
                                Manifest.permission.BLUETOOTH_SCAN) != PackageManager.PERMISSION_GRANTED) {
                    ActivityCompat.requestPermissions(MainActivity.this,
                            new String[] { Manifest.permission.BLUETOOTH_CONNECT, Manifest.permission.BLUETOOTH_SCAN },
                            PERMISSION_ALL);
                    return false;
                }
            } else {
                if (ContextCompat.checkSelfPermission(MainActivity.this,
                        Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
                    ActivityCompat.requestPermissions(MainActivity.this,
                            new String[] { Manifest.permission.ACCESS_FINE_LOCATION }, PERMISSION_ALL);
                    return false;
                }
            }
            return true;
        }
    }
}
