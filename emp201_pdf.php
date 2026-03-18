<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <h2 style="text-align: center;">Em201 Details</h2>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">YEAR END</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">CLIENT</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">REFERENCES</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">TAX</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">PAY</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">SDL</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">UIF</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">LIABILITY</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">PENALTIES</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">TOTAL DUE</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">AMOUNT</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">DATE PAID</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">OUTSTANDING</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">COMPLAINT</th>
                <th style="border: 1px solid #000; padding: 15px; text-align: center; background-color: #2D4154; color: #ffffff; height: 50px; position: relative; top: 5px;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->pay_period) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->paye_number) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->payment_reference) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->pay_period) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->pay_liability) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->sdl_liability) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->uif_liability) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->pay_liability + $emp_data->sdl_liability + $emp_data->uif_liability) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->penalty) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->tax_payable) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->other) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->payment_date) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->tax_payable - $emp_data->other) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= htmlspecialchars($emp_data->income_tax_number) ?></td>
                <td style="border: 1px solid #000; padding: 15px; text-align: center; height: 50px; position: relative; top: 5px;"><?= $emp_data->active ? 'Active' : 'Inactive' ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
